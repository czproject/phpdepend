<?php

	namespace CzProject\PhpDepend;


	class PhpToken
	{
		/** @var int|string */
		private $id;

		/** @var string */
		private $text;

		/** @var bool */
		private $simple;


		/**
		 * @param int|string $id
		 * @param string $text
		 * @param bool $simple
		 */
		public function __construct($id, $text, $simple)
		{
			$this->id = $id;
			$this->text = $text;
			$this->simple = $simple;
		}


		/**
		 * @param  int|string $id
		 * @return bool
		 */
		public function is($id)
		{
			return $this->id === $id;
		}


		/**
		 * @param  string $text
		 * @return bool
		 */
		public function isText($text)
		{
			return $this->text === $text;
		}


		/**
		 * @return int|string
		 */
		public function getId()
		{
			return $this->id;
		}


		/**
		 * @return string
		 */
		public function getText()
		{
			return $this->text;
		}


		/**
		 * @return bool
		 */
		public function isSimple()
		{
			return $this->simple;
		}


		/**
		 * @return bool
		 */
		public function isComplex()
		{
			return !$this->simple;
		}
	}
