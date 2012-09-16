<?php

/**
 * German defaults template
 * @author Julian
 *
 *TODO:
 *- In Variablen auslagern (am besten hier alles was nicht Zend Db spezifisch ist in Abstract.php
 *
 */
class Model_ClassGenerator_Defaults_German1 extends Model_ClassGenerator_Defaults_Abstract{
		
	/**
	 * Creates a new template instance.
	 * 
	 * @return models_ClassGenerator_Defaults_German1
	 */
	public static function createInstance(){
		return new self();	
	}
}