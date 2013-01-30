<?php
	/** Cz PHP Depend Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-01-29-1
	 */
	
	namespace Cz;
	
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
		
		/** @var  bool */
		private $kill;
		
		/** @var  array */
		private $tokens;
		
		/** @var  int */
		private $level;
		
		/** @var  array */
		private $use;
		
		private static $T_NAMESPACE;
		private static $T_NS_SEPARATOR;
		private static $T_TRAIT;
		
		
		
		public function __construct()
		{
			self::$T_NAMESPACE = PHP_VERSION_ID < 50300 ? -1 : T_NAMESPACE;
			self::$T_NS_SEPARATOR = PHP_VERSION_ID < 50300 ? -1 : T_NS_SEPARATOR;
			self::$T_TRAIT = PHP_VERSION_ID < 50400 ? -1 : T_TRAIT;
		}
		
		
		
		/**
		 * @return	string[]|NULL
		 */
		public function getClasses()
		{
			return $this->classes;
		}
		
		
		
		/**
		 * @return	string[]|NULL
		 */
		public function getDependencies()
		{
			return $this->dependencies;
		}
		
		
		
		/**
		 * @param	string
		 * @return	void
		 */
		public function parse($str)
		{
			$this->tokens = $this->tokensFromSource($str);
			$this->inClass = FALSE;
			$this->namespace = '';
			$this->classes = array();
			$this->dependencies = array();
			$this->kill = FALSE;
			$this->level = 0;
			
			while($token = $this->next())// && !$this->kill)
			{
				$tokenId = is_array($token) ? $token[0] : $token;
				
				switch($tokenId)
				{
					// depend
					case T_NEW:
					case T_EXTENDS:
						$this->addDependency($this->readName());
						continue;
					
					case T_DOUBLE_COLON:
						$this->addDependency($this->readStaticClass());
						continue;
					
					case T_IMPLEMENTS:
						$this->addDependency($this->readImplements());
						continue;
					
					// define
					case T_CLASS:
					case self::$T_TRAIT:
						$this->inClass = TRUE;
						
					case T_INTERFACE:
						$this->addClass($this->namespace . '\\' . $this->readIdentifier());
						continue;
					
					// namespace
					case self::$T_NAMESPACE:
						$this->namespace = $this->readIdentifier();
						continue;
					
					// USE keywords
					case T_USE:
						if($this->inClass) // trait
						{
							$this->addDependency($this->readTrait());
						}
						else // namespace
						{
							$this->use = $this->readUse();
						}
						continue;
					
					case '{':
						$this->level++;
						
						if($this->inClass === TRUE)
						{
							$this->inClass = $this->level;
						}
						continue;
					
					case '}':
						if($this->inClass === $this->level)
						{
							$this->inClass = FALSE;
						}
						
						$this->level--;
						continue;
				}
			}
		}
		
		
		
		/**
		 * @param	string
		 * @return	array
		 */
		private function tokensFromSource($str)
		{
			return token_get_all($str);
		}
		
		
		
		/**
		 * @param	string|string[]
		 * @return	$this
		 */
		private function addClass($class)
		{
			if($class)
			{
				if(!is_array($class))
				{
					$class = array($class);
				}
			
				foreach($class as $name)
				{
					$this->classes[] = trim($name, '\\');
				}
			}
			
			return $this;
		}
		
		
		
		/**
		 * @param	string|string[]
		 * @return	$this
		 */
		private function addDependency($class)
		{
			if($class)
			{
				if(!is_array($class))
				{
					$class = array($class);
				}
			
				foreach($class as $name)
				{
					$this->dependencies[trim($name, '\\')] = TRUE;
				}
			}
			
			return $this;
		}
		
		
		
		/**
		 * @return	string|array|FALSE
		 */
		private function next()
		{
			if($this->kill)
			{
				return FALSE;
			}
			
			$next = current($this->tokens);
			next($this->tokens);
			return $next;
		}
		
		
		
		/**
		 * @return	string|array|FALSE
		 */
		private function prev()
		{
			return prev($this->tokens);
		}
		
		
		
		/**
		 * @return	string|FALSE
		 */
		private function readName()
		{
			return $this->expandName($this->readIdentifier());
		}
		
		
		
		/**
		 * @return	string[]
		 */
		private function readImplements()
		{
			$implements = array();
			
			while(($name = $this->readName()) !== FALSE)
			{
				$implements[] = $name;
				$token = $this->next();
				
				if($token !== ',' && (!is_array($token) && $token[0] !== T_WHITESPACE))
				{
					$this->prev(); // TODO:??
					break;
				}
			}
			
			return $implements;
		}
		
		
		
		/**
		 * @return	string
		 */
		private function readIdentifier()
		{
			$name = FALSE;
			while($token = $this->next())
			{
				if(!is_array($token))
				{
					$this->prev();
					break;
				}
				
				switch($token[0])
				{
					case T_STRING:
					case self::$T_NS_SEPARATOR:
						$name .= $token[1];
					
					case T_WHITESPACE:
						continue;
					
					default:
						$this->prev();
						return $name;
				}
			}
			
			return $name;
		}
		
		
		
		/**
		 * @return	array  [short-name => full-class-name, ...]
		 */
		private function readUse()
		{
			$use = array();
			$short = FALSE;
			while($name = $this->readIdentifier())
			{
				$short = self::generateShort($name, TRUE);
				$token = $this->next();
				
				
				if(is_array($token))
				{
					if($token[0] === T_AS)
					{
						$short = $this->readIdentifier();
						$token = $this->next();
					}
				}
				
				if($token === ',' || $token === ';')
				{
					$use[$short] = $name;
					$short = FALSE;
				}
				
			}
			
			return $use;
		}
		
		
		
		private function readStaticClass()
		{
			$name = FALSE;
			$i = 0;
			
			while($token = $this->prev())
			{
				$i++;
				if(is_array($token) && ($token[0] === T_STRING || $token[0] === self::$T_NS_SEPARATOR))
				{
					if(!($token[1] === 'self' || $token[1] === 'parent' || $token[1] === 'static'))
					{
						$name = $token[1] . $name;
						continue;
					}
				}
				
				break;
			}
			
			if($name !== FALSE)
			{
				$name = $this->expandName($name);
			}
			
			while($i > 0)
			{
				$this->next();
				$i--;
			}
			
			return $name;
		}
		
		
		
		private function readTrait()
		{
			$traits = array();
			
			while($name = $this->readName())
			{
				$traits[] = $name;
				$token = $this->next();
				
				if($token === ',' || $token === ';' || $token === '{')
				{
					if($token === ';')
					{
						break;
					}
					
					if($token === '{')
					{
						$level = 0;
						
						while($t = $this->next())
						{
							if($t === '{')
							{
								$level++;
							}
							elseif($t === '}')
							{
								$level--;
								
								if($level === 0)
								{
									return $traits;
								}
							}
						}
					}
				}
				else
				{
					break;
				}
			}
			
			return $traits;
		}
		
		
		
		/**
		 * @param	string
		 * @return	string
		 */
		private function expandName($name)
		{
			if($name[0] === '\\' || !$name)
			{
				return $name;
			}
			else
			{
				$short = self::generateShort($name);
				
				if(isset($this->use[$short]))
				{
					return $this->use[$short] . '\\' . substr($name, strlen($short)+1);
				}
			}
			return $this->namespace . '\\' . $name;
		}
		
		
		
		private static function generateShort($name, $fromRight = FALSE)
		{
			$short = trim($name, '\\');
			$pos = $fromRight ? strrpos($short, '\\') : strpos($short, '\\');
			
			if($pos !== FALSE)
			{
				if($fromRight)
				{
					$short = substr($short, $pos + 1);
				}
				else
				{
					$short = substr($short, 0, $pos);
				}
			}
			
			return $short;
		}
	}

