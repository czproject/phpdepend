<?php

	namespace CzProject\PhpDepend;


	class PhpTokens
	{
		/** @var PhpToken[] */
		private $tokens;


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
		}


		/**
		 * @return PhpToken|NULL
		 */
		public function next()
		{
			$next = current($this->tokens);
			next($this->tokens);
			return $next !== FALSE ? $next : NULL;
		}


		/**
		 * @return PhpToken|NULL
		 */
		public function prev()
		{
			$token = prev($this->tokens);
			return $token !== FALSE ? $token : NULL;
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
