<?php

class Model_Library_Messages_States_Error implements Model_Library_Messages_States_IState{
	
	protected static $instance;
	
	private $name = 'error';
	private $icon = 'errorIcon';
	
	public static function createInstance(){
		if(self::$instance == null){
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public function __toString(){
		return $this->getName();
	}
	
	public function getIcon(){
		return $this->icon;
	}
		
	public function getName(){
		return $this->name;
	}
}

?>