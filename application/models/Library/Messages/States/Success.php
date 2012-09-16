<?php

class Model_Library_Messages_States_Success implements Model_Library_Messages_States_IState {
	
	protected static $instance;
	
	private $name = 'success';
	private $icon = 'successIcon';
	
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