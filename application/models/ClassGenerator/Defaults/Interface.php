<?php

/**
 *	Template containing default values for class persistence.
 */
interface Model_ClassGenerator_Defaults_Interface {
	/**
	 * Returns the "createInstance"-Method.
	 * 
	 * @return Zend_CodeGenerator_Php_Method
	 */
	public function createMethodCreateInstance();
	
	/**
	 * Returns the "__construct"-Method.
	 * 
	 * @return Zend_CodeGenerator_Php_Method
	 */
	public function createMethodConstruct();
	
	/**
	 * Returns the "load"-Method.
	 * 
	 * @return Zend_CodeGenerator_Php_Method
	 */
	public function createMethodLoad();
		
	/**
	 * Returns the "delete"-Method.
	 * 
	 * @return Zend_CodeGenerator_Php_Method
	 */
	public function createMethodDelete();	
	
	/**
	 * Returns the "saveNew"-Method.
	 * 
	 * @return Zend_CodeGenerator_Php_Method
	 */
	public function createMethodSaveNew();
	
	/**
	 * Returns the "save"-Method.
	 * 
	 * @return Zend_CodeGenerator_Php_Method
	 */
	public function createMethodSave();
	
	public function createMethodIsSaved();
	
	/**
	 * Returns the "saveUpdate"-Method.
	 * 
	 * @return Zend_CodeGenerator_Php_Method
	 */
	public function createMethodSaveUpdate();
	
	/**
	 * Returns the "toArray"-Method.
	 * 
	 * public function toArray(){
	 *     return array(
	 *         SQL_COLUMN => $this->var,
	 *         ...
	 *     );
	 * }
	 * 
	 * @return Zend_CodeGenerator_Php_Method
	 */
	public function createMethodToArray();
	
	/**
	 * Returns the Constants that encapsulate the SQL properties.
	 * 
	 * @return Zend_CodeGenerator_Php_Parameter[]
	 */
	public function createAttributesSqlConstants();
	
	/**
	 * Adds the instances cache Attribute.
	 * 
	 * @return Zend_CodeGenerator_Php_Parameter[]
	 */
	public function createAttributeInstances();
		
	public function createAttributeDbObj();
	
	/**
	 * Returns the CREATE TABLE - SQL Statement.
	 * 
	 * @return string
	 */
	public function createSqlCreateTable();
	
	/**
	 * Returns the persistence information container.
	 * 
	 * @return models_ClassGenerator_PersistenceInformation
	 */
	public function getPersistenceInformation();
	
	/**
	 * Returns the class to manipulate.
	 * 
	 * @return Zend_CodeGenerator_Php_Class
	 */
	public function getClass();
	
	/**
	 * Sets the class to manipulate.
	 * 
	 * @param Zend_CodeGenerator_Php_Class $class
	 */
	public function setClass(Zend_CodeGenerator_Php_Class $class);
}