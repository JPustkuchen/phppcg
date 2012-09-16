<?php

/**
 * Adds persistence functionality to the given class.
 * 
 * @author Julian Pustkuchen
 * @since 170710
 * 
 * TODO:
 * - Exceptions wenn nötige Tags nicht gefunden werden?
 */
class Model_ClassGenerator_Persistence {
	/**
	 * The @Column - Tag identifier.
	 * Style: @Column(name=[COLUMN])
	 * 
	 * @var string
	 */
	public static $tagColumn = 'Column';
	
	/**
	 * The @Table - Tag identifier.
	 * Style: @Table(name=DATABASE_TABLE)
	 * 
	 * @var string
	 */
	public static $tagTable = 'Table';
	
	/**
	 * The @Serial - Tag identifier.
	 * Style: @Serial
	 * 
	 * Marks a field as serial field.
	 * 
	 * @var string
	 */
	public static $tagSerial = 'Serial';
	
	/**
	 * The @Pkey - Tag identifier.
	 * Style: @Pkey
	 * 
	 * Marks a field as primary key field.
	 * 
	 * @var string
	 */
	public static $tagPkey = 'Pkey';
	
	/**
	 * The given class to add persistence to.
	 * 
	 * @var $class Zend_CodeGenerator_Php_Class
	 */
	private $class;
	
	/**
	 * The resulting class with added persistence.
	 * 
	 * @var $class Zend_CodeGenerator_Php_Class
	 */
	private $result;
	
	/**
	 * The template containing the defaults.
	 * 
	 * @var $defaults Model_ClassGenerator_Defaults_Interface
	 */
	private $defaults;
	
	/**
	 * The persistence information container.
	 * 
	 * @var Model_ClassGenerator_PersistenceInformation
	 */
	private $persistenceInformation;
		
	/**
	 * Creates a new instance.
	 * 
	 * @param Zend_CodeGenerator_Php_Class $class
	 * @param Model_ClassGenerator_Defaults_Interface $defaults
	 * @return Model_ClassGenerator_Persistence
	 */
	public static function createInstance(Zend_CodeGenerator_Php_Class $class, Model_ClassGenerator_Defaults_Interface $defaults = null){
		return new self($class, $defaults);
	}
	
	private function __construct(Zend_CodeGenerator_Php_Class $class, Model_ClassGenerator_Defaults_Interface $defaults = null){
		$this->class = $class;
		if($defaults !== null){
			$this->defaults = $defaults;
		} else {
			$this->defaults = Model_ClassGenerator_Defaults_German1::createInstance();
		}
		$this->defaults->setClass($class);
		$this->persistenceInformation = $this->defaults->getPersistenceInformation();		
	}
	
	/**
	 * Checks the preconditions.
	 * 
	 * @throws Exception if check fails.
	 */
	private function _checkPreconditions(){
		
	}
	
	/**
	 * Checks the postconditions.
	 * 
	 * @throws Exception if check fails.
	 */
	private function _checkPostconditions(){
		if(!$this->persistenceInformation->hasTableName()){
			throw new Exception('No table name set!');
		}
		
		if(!$this->persistenceInformation->hasSerialAttribute()){
			throw new Exception('No serial attribute set!');
		}
		
		if(!$this->persistenceInformation->hasAttributesToPersist()){
			throw new Exception('No attributes to persist!');
		}
	}
	
	private function _checkClass(){
		$docblock = $this->class->getDocblock();
		if($docblock){
			$tags = $docblock->getTags();	
			//Check if a "table"-Tag is set
			foreach($tags as $tag){
				/* @var $tag Zend_CodeGenerator_Php_Docblock_Tag */
				if($tag->getName() === self::$tagTable){
					//Persistence Tag found
					$this->persistenceInformation->setTableInformation($tag->getDescription());
				}
			}
		}		
	}
	
	/**
	 * Checks all attributes contained if they contain
	 * the persistence-tag to be persisted.
	 * 
	 * Adds those attributes to the persistenceInformation - Container.
	 */
	private function _checkAttributes(){
		$attributes = $this->class->getProperties();
		if(!empty($attributes)){
			foreach($attributes as $attribute){
				//Iterate over all attributes
				/* @var $attribute Zend_CodeGenerator_Php_Property */ 
				$docblock = $attribute->getDocblock();
				if($docblock){
					$tags = $docblock->getTags();
	
					//Check if a "Column"-Tag is set
					foreach($tags as $tag){						
						/* @var $tag Zend_CodeGenerator_Php_Docblock_Tag */
						if($tag->getName() === self::$tagColumn){
							//Column Tag found
							$this->persistenceInformation->addAttributeToPersist($attribute);
						}
						
						if($tag->getName() === self::$tagSerial){
							//Column Tag found
							$this->persistenceInformation->addSerialAttribute($attribute);
						}
						
						if($tag->getName() === self::$tagPkey){
							//Pkey Tag found
							$this->persistenceInformation->addPkeyAttribute($attribute);
						}
					}
				}
			}
		}
	}
	
	/**
	 * Helper function to truely create the persistence.
	 * Overwrites already existing persistence behaviour.
	 */
	private function _createPersistence(){
		$this->_checkPreconditions();
		$this->_checkClass();
		$this->_checkAttributes();
		$this->_checkPostconditions();
				
		//Clone class
		//TODO - testen ob auch subelemente geklont werden!
		$this->result = clone($this->class);
		$this->_createPersistMethods();
		$this->_createPersistComments();
	}
	
	private function _createPersistAttributes(){
		$this->defaults->createSqlConstants();
	}
	
	private function _createPersistMethods(){
		$this->defaults->createAttributesSqlConstants();
		$this->defaults->createMethodCreateInstance();
		$this->defaults->createMethodLoad();
		$this->defaults->createMethodConstruct();
		$this->defaults->createAttributeInstances();
		$this->defaults->createMethodDelete();		
		$this->defaults->createMethodSave();
		$this->defaults->createMethodSaveNew();
		$this->defaults->createMethodSaveUpdate();
		$this->defaults->createMethodToArray();		
	}
	
	private function _createPersistComments(){
		$this->defaults->createSqlCreateTable();
	}
	
	/**
	 * Creates the persistence and returns the result.
	 * 
	 * @return Zend_CodeGenerator_Php_Class
	 */
	public function createPersistence(){
		if($this->result === null){
			$this->_createPersistence();
		}
		
		return $this->result;
	}
}