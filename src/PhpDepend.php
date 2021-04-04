<?php

	namespace CzProject\PhpDepend;


	class PhpDepend
	{
		/** @var  bool|int */
		private $inClass = FALSE;

		/** @var  string */
		private $namespace = '';

		/** @var  string[] */
		private $classes;

		/** @var  string[] */
		private $dependencies;

		/** @var  PhpTokens */
		private $tokens;

		/** @var  int */
		private $level;

		/** @var  array */
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
		 * @param  string
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
		 * @param  string
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
						$this->namespace = $this->readIdentifier();
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
		 * @param  string|string[]
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
		 * @param  string|string[]
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
			return $this->expandName($this->readIdentifier(TRUE));
		}


		/**
		 * @return string[]
		 */
		private function readImplements()
		{
			$implements = [];

			while (($name = $this->readName()) !== FALSE) {
				$implements[] = $name;
				$token = $this->tokens->next();

				if (!$token->is(',') && ($token->isSimple() && !$token->is(T_WHITESPACE))) {
					$this->tokens->prev(); // TODO:??
					break;
				}
			}

			return $implements;
		}


		/**
		 * @param  bool
		 * @return string
		 */
		private function readIdentifier($readNamespaceKeyword = FALSE)
		{
			$name = FALSE;

			while ($token = $this->tokens->next()) {
				if ($token->isSimple()) {
					$this->tokens->prev();
					break;
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
		 * @return array  [short-name => full-class-name, ...]
		 */
		private function readUse()
		{
			$use = [];
			$short = FALSE;

			while ($name = $this->readIdentifier()) {
				$token = $this->tokens->next();
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
							$token = $this->tokens->next();
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
		 * @param  string
		 * @return mixed|NULL  token or NULL
		 */
		private function readUseGroup($rootName, array &$uses)
		{
			$token = NULL;
			$rootName = rtrim($rootName, '\\') . '\\';

			while ($name = $this->readIdentifier()) {
				$short = self::generateShort($name, TRUE);
				$token = $this->tokens->next();

				if ($token->isComplex()) {
					if ($token->is(T_AS)) {
						$short = $this->readIdentifier();
						$token = $this->tokens->next();
					}
				}

				if ($token->is(',') || $token->is('}')) {
					$uses[$short] = $rootName . $name;
					$short = FALSE;

					if ($token->is('}')) {
						$token = $this->tokens->next();
						break;
					}
				}
			}

			return $token;
		}


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
				$this->tokens->next();
				$i--;
			}

			$this->tokens->next(); // consume content after T_DOUBLE_COLON
			return $name;
		}


		private function readTrait()
		{
			$traits = [];

			while ($name = $this->readName()) {
				$traits[] = $name;
				$token = $this->tokens->next();

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
		 * @param  string
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
