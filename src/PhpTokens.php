<?php

	namespace CzProject\PhpDepend;


	class PhpTokens
	{
		/** @var PhpToken[] */
		private $tokens;

		/** @var int */
		private $position = 0;


		/**
		 * @param list<array{0: int, 1: string}|string> $tokens
		 */
		public function __construct(array $tokens)
		{
			$this->tokens = [];

			foreach ($tokens as $token) {
				if (is_string($token)) {
					$this->tokens[] = new PhpToken($token, $token, TRUE);

				} else {
					$this->tokens[] = new PhpToken($token[0], $token[1], FALSE);
				}
			}

			if (empty($this->tokens)) {
				throw new InvalidArgumentException('Tokens cannot be empty.');
			}
		}


		/**
		 * @return PhpToken|NULL
		 */
		public function next()
		{
			if (!isset($this->tokens[$this->position])) {
				return NULL;
			}

			$next = $this->tokens[$this->position];
			$this->nextPosition();
			return $next;
		}


		/**
		 * @return PhpToken
		 */
		public function nextToken()
		{
			$token = $this->next();

			if ($token === NULL) {
				throw new InvalidStateException('There is no next token.');
			}

			return $token;
		}


		/**
		 * @return PhpToken
		 */
		public function prev()
		{
			$this->prevPosition();
			return $this->tokens[$this->position];
		}


		/**
		 * @return void
		 */
		private function nextPosition()
		{
			if (($this->position + 1) > count($this->tokens)) {
				throw new InvalidStateException('There no next position.');
			}

			$this->position++;
		}


		/**
		 * @return void
		 */
		private function prevPosition()
		{
			if ($this->position === 0) {
				throw new InvalidStateException('There no prev position.');
			}

			$this->position--;
		}


		/**
		 * @param  string $str
		 * @return self
		 */
		public static function fromSource($str)
		{
			return new self(token_get_all($str));
		}
	}
