<?php

	declare(strict_types=1);

	namespace CzProject\PhpDepend;


	class PhpDepend
	{
		/** @var  bool|int */
		private $inClass = FALSE;

		/** @var  string */
		private $namespace = '';

		/** @var  string[] */
		private $classes;

		/** @var  array<string, TRUE> */
		private $dependencies;

		/** @var  PhpTokens */
		private $tokens;

		/** @var  int */
		private $level;

		/** @var  array<string, string> */
		private $use;


		/**
		 * Returns list of defined classes, interfaces & traits or NULL.
		 * @return string[]|NULL
		 */
		public function getClasses()
		{
			return $this->classes;
		}


		/**
		 * Returns list of required classes, interfaces & traits or NULL.
		 * @return string[]|NULL
		 */
		public function getDependencies()
		{
			return array_keys($this->dependencies);
		}


		/**
		 * Parses content of PHP file.
		 * @param  string $filename
		 * @return bool  FALSE => file error
		 */
		public function parseFile($filename)
		{
			$source = file_get_contents($filename);

			if ($source !== FALSE) {
				$this->parse($source);
				return TRUE;
			}

			return FALSE;
		}


		/**
		 * Parses given PHP code.
		 * @param  string $str
		 * @return void
		 */
		public function parse($str)
		{
			$this->tokens = PhpTokens::fromSource($str);
			$this->inClass = FALSE;
			$this->namespace = '';
			$this->classes = [];
			$this->dependencies = [];
			$this->use = [];
			$this->level = 0;

			while ($token = $this->tokens->next()) {
				switch ($token->getId()) {
					// depend
					case T_NEW:
					case T_EXTENDS:
						$this->addDependency($this->readName());
						break;

					case T_DOUBLE_COLON:
						$this->addDependency($this->readStaticClass());
						break;

					case T_IMPLEMENTS:
						$this->addDependency($this->readImplements());
						break;

					// define
					case T_CLASS:
					case T_TRAIT:
						$this->inClass = TRUE;

					case T_INTERFACE:
						$this->addClass($this->namespace . '\\' . $this->readIdentifier());
						break;

					// namespace
					case T_NAMESPACE:
						$this->namespace = (string) $this->readIdentifier();
						$this->use = [];
						break;

					// USE keywords
					case T_USE:
						if ($this->inClass) { // trait
							$this->addDependency($this->readTrait());

						} else { // namespace
							$this->use = array_merge($this->use, $this->readUse());
						}

						break;

					case '{':
						$this->level++;

						if ($this->inClass === TRUE) {
							$this->inClass = $this->level;
						}

						break;

					case '}':
						if ($this->inClass === $this->level) {
							$this->inClass = FALSE;
						}

						$this->level--;
						break;
				}
			}
		}


		/**
		 * @param  string|string[]|FALSE $class
		 * @return $this
		 */
		private function addClass($class)
		{
			if ($class) {
				if (!is_array($class)) {
					$class = [$class];
				}

				foreach ($class as $name) {
					$name = trim($name, '\\');

					if ($name !== '') {
						$this->classes[] = $name;
					}
				}
			}

			return $this;
		}


		/**
		 * @param  string|string[]|FALSE $class
		 * @return $this
		 */
		private function addDependency($class)
		{
			if ($class) {
				if (!is_array($class)) {
					$class = [$class];
				}

				foreach ($class as $name) {
					$name = trim($name, '\\');

					if ($name !== '') {
						$this->dependencies[$name] = TRUE;
					}
				}
			}

			return $this;
		}


		/**
		 * @return string|FALSE
		 */
		private function readName()
		{
			$name = $this->readIdentifier(TRUE);

			if ($name === FALSE) {
				return FALSE;
			}

			return $this->expandName($name);
		}


		/**
		 * @return string[]
		 */
		private function readImplements()
		{
			$implements = [];

			while (($name = $this->readName()) !== FALSE) {
				$implements[] = $name;
				$token = $this->tokens->nextToken();

				if (!$token->is(',') && ($token->isSimple() && !$token->is(T_WHITESPACE))) {
					$this->tokens->prev(); // TODO:??
					break;
				}
			}

			return $implements;
		}


		/**
		 * @param  bool $readNamespaceKeyword
		 * @return string|FALSE
		 */
		private function readIdentifier($readNamespaceKeyword = FALSE)
		{
			$name = FALSE;

			while ($token = $this->tokens->next()) {
				if ($token->isSimple()) {
					$this->tokens->prev();
					break;
				}

				if (PHP_VERSION_ID >= 80000) {
					if ($token->is(T_NAME_QUALIFIED)) {
						$name = $token->getText();
						continue;
					}

					if ($token->is(T_NAME_FULLY_QUALIFIED)) {
						$name = $token->getText();
						continue;
					}

					if ($token->is(T_NAME_RELATIVE)) {
						$name = '\\' . $this->namespace . '\\' . substr($token->getText(), 10);
						continue;
					}
				}

				if ($readNamespaceKeyword && $token->is(T_NAMESPACE)) {
					$name = '\\' . $this->namespace;
					continue;
				}

				switch ($token->getId()) {
					case T_STRING:
					case T_NS_SEPARATOR:
						$readNamespaceKeyword = FALSE;
						$name .= $token->getText();

					case T_WHITESPACE:
						break;

					default:
						$this->tokens->prev();
						return $name;
				}
			}

			return $name;
		}


		/**
		 * @return array<string, string>  [short-name => full-class-name, ...]
		 */
		private function readUse()
		{
			$use = [];
			$short = FALSE;

			while ($name = $this->readIdentifier()) {
				$token = $this->tokens->nextToken();
				$wasGroup = FALSE;

				if ($token->is('{')) { // group statement
					$wasGroup = TRUE;
					$nextToken = $this->readUseGroup($name, $use);

					if ($nextToken !== NULL) {
						$token = $nextToken;
					}

				} else {
					$short = self::generateShort($name, TRUE);

					if ($token->isComplex()) {
						if ($token->is(T_AS)) {
							$short = $this->readIdentifier();
							$token = $this->tokens->nextToken();
						}
					}
				}

				if (!$wasGroup && ($token->is(',') || $token->is(';'))) {
					$use[$short] = $name;
					$short = FALSE;
				}
			}

			return $use;
		}


		/**
		 * @param  string $rootName
		 * @param  array<string, string> $uses
		 * @return PhpToken|NULL  token or NULL
		 */
		private function readUseGroup($rootName, array &$uses)
		{
			$token = NULL;
			$rootName = rtrim($rootName, '\\') . '\\';

			while ($name = $this->readIdentifier()) {
				$short = self::generateShort($name, TRUE);
				$token = $this->tokens->nextToken();

				if ($token->isComplex()) {
					if ($token->is(T_AS)) {
						$short = $this->readIdentifier();
						$token = $this->tokens->nextToken();
					}
				}

				if ($token->is(',') || $token->is('}')) {
					$uses[$short] = $rootName . $name;
					$short = FALSE;

					if ($token->is('}')) {
						$token = $this->tokens->nextToken();
						break;
					}
				}
			}

			return $token;
		}


		/**
		 * @return string|FALSE
		 */
		private function readStaticClass()
		{
			$name = FALSE;
			$i = 0;

			while ($token = $this->tokens->prev()) {
				$i++;

				if ($token->isComplex()) {
					if ($token->is(T_DOUBLE_COLON)) {
						continue;
					}

					if (PHP_VERSION_ID >= 80000) {
						if ($token->is(T_NAME_QUALIFIED) || $token->is(T_NAME_FULLY_QUALIFIED)) {
							$name = $token->getText();
							break;
						}
					}

					if (($token->is(T_STRING) || $token->is(T_NS_SEPARATOR))
						&& !($token->isText('self') || $token->isText('parent') || $token->isText('static'))) {
						$name = $token->getText() . $name;
						continue;
					}
				}

				break;
			}

			if ($name !== FALSE) {
				$name = $this->expandName($name);
			}

			while ($i > 0) {
				$this->tokens->nextToken();
				$i--;
			}

			$this->tokens->nextToken(); // consume content after T_DOUBLE_COLON
			return $name;
		}


		/**
		 * @return string[]
		 */
		private function readTrait()
		{
			$traits = [];

			while ($name = $this->readName()) {
				$traits[] = $name;
				$token = $this->tokens->nextToken();

				if ($token->is(',') || $token->is(';') || $token->is('{')) {
					if ($token->is(';')) {
						break;
					}

					if ($token->is('{')) {
						$level = 0;

						while ($t = $this->tokens->next()) {
							if ($t->is('{')) {
								$level++;

							} elseif($t->is('}')) {
								$level--;

								if ($level < 1) {
									return $traits;
								}
							}
						}
					}

				} else {
					break;
				}
			}

			return $traits;
		}


		/**
		 * @param  string $name
		 * @return string
		 */
		private function expandName($name)
		{
			if ($name[0] === '\\' || !$name) {
				return $name;

			} else {
				$short = self::generateShort($name);

				if (isset($this->use[$short])) {
					return $this->use[$short] . '\\' . substr($name, strlen($short)+1);
				}
			}

			return $this->namespace . '\\' . $name;
		}


		/**
		 * @param  string $name
		 * @param  bool $fromRight
		 * @return string
		 */
		private static function generateShort($name, $fromRight = FALSE)
		{
			$short = trim($name, '\\');
			$pos = $fromRight ? strrpos($short, '\\') : strpos($short, '\\');

			if ($pos !== FALSE) {
				if ($fromRight) {
					$short = substr($short, $pos + 1);

				} else {
					$short = substr($short, 0, $pos);
				}
			}

			return $short;
		}
	}
