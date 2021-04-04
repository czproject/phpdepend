<?php

	namespace CzProject\PhpDepend;


	class PhpTokens
	{
		/** @var  array */
		private $tokens;


		public function __construct(array $tokens)
		{
			$this->tokens = $tokens;
		}


		/**
		 * @return string|array|FALSE
		 */
		public function next()
		{
			$next = current($this->tokens);
			next($this->tokens);
			return $next;
		}


		/**
		 * @return string|array|FALSE
		 */
		public function prev()
		{
			return prev($this->tokens);
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
