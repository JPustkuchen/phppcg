<?php

/**
 * @author Julian
 *
 *TODO:
 *- Nur ein Serial!
 */
abstract class Model_ClassGenerator_Defaults_Abstract implements Model_ClassGenerator_Defaults_Interface{
	/**
	 * The persistence information container.
	 * 
	 * @var $persistenceInformation Model_ClassGenerator_PersistenceInformation
	 */
	protected $persistenceInformation;
	
	/**
	 * The given class to manipulate.
	 * 
	 * @var $class Zend_CodeGenerator_Php_Class 
	 */
	protected $class;
	
	protected function __construct() {
		$this->persistenceInformation = Model_ClassGenerator_PersistenceInformation::createInstance();
	}
	
	/**
	 * @see models_ClassGenerator_Defaults_Interface::createMethodCreateInstance
	 */
	public function createMethodCreateInstance(){
		$resultMethod = new Zend_CodeGenerator_Php_Method();
		$resultMethod->setName('createInstance');
		$resultMethod->setStatic(true);
		$resultMethod->setVisibility(Zend_CodeGenerator_Php_Property::VISIBILITY_PUBLIC);
				
		$typeTag = new Zend_CodeGenerator_Php_Docblock_Tag();
		$typeTag->setName('return');
		$typeTag->setDescription($this->getClass()->getName());
		$docblock = new Zend_CodeGenerator_Php_Docblock();
		$resultMethod->setDocblock($docblock);
		$docblock->setTag($typeTag);
		$docblock->setLongDescription('Returns a new instance of '.$this->getClass()->getName().'.');

		$parameterZendDbObj = new Zend_CodeGenerator_Php_Parameter();
		$parameterZendDbObj->setName('parZendDbObj');
		$parameterZendDbObj->setType('Zend_Db_Adapter_Abstract');
		$resultMethod->setParameter($parameterZendDbObj);
		
		$body = 'return new self($parZendDbObj);';
		
		$resultMethod->setBody($body);
		
		$this->getClass()->setMethod($resultMethod);
	}
	
	/**
	 * @see models_ClassGenerator_Defaults_Interface::createAttributeInstance
	 */
	public function createAttributeInstances(){
		$resultAttribute = new Zend_CodeGenerator_Php_Property();
		$resultAttribute->setName('instances');
		$resultAttribute->setDefaultValue('array()');
		$typeTag = new Zend_CodeGenerator_Php_Docblock_Tag();
		$typeTag->setName('var');
		$typeTag->setDescription($this->getClass()->getName().'[]');
		$docblock = new Zend_CodeGenerator_Php_Docblock();
		$docblock->setLongDescription('Singleton: The objects indexed by their primary keys.');
		$resultAttribute->setDocblock($docblock);
		$docblock->setTag($typeTag);
		
		$this->getClass()->setProperty($resultAttribute);
	}
	
	public function createAttributeDbObj(){
		$resultAttribute = new Zend_CodeGenerator_Php_Property();
		$resultAttribute->setName('zendDbObj');
		
		$typeTag = new Zend_CodeGenerator_Php_Docblock_Tag();
		$typeTag->setName('var');
		$typeTag->setDescription('Zend_Db_Adapter_Abstract');
		$docblock = new Zend_CodeGenerator_Php_Docblock();
		$resultAttribute->setDocblock($docblock);
		$docblock->setTag($typeTag);
		
		//TODO -Comments!
		$this->getClass()->setProperty($resultAttribute);
	}
	
	/**
	 * @see models_ClassGenerator_Defaults_Interface::createMethodCreateInstance
	 */
	public function createMethodConstruct(){
		$resultMethod = new Zend_CodeGenerator_Php_Method();
		$resultMethod->setName('__construct');
		$resultMethod->setVisibility(Zend_CodeGenerator_Php_Property::VISIBILITY_PRIVATE);
		
		$parameterZendDbObj = new Zend_CodeGenerator_Php_Parameter();
		$parameterZendDbObj->setName('parZendDbObj');
		$parameterZendDbObj->setType('Zend_Db_Adapter_Abstract');
		$resultMethod->setParameter($parameterZendDbObj);
		
		$body = '$this->zendDbObj = $parZendDbObj;';
		$resultMethod->setBody($body);
		
		$this->getClass()->setMethod($resultMethod);
	}

	/**
	 * @see models_ClassGenerator_Defaults_Interface::createMethodLoad
	 */
	public function createMethodLoad(){
		$resultMethod = new Zend_CodeGenerator_Php_Method();
		$resultMethod->setName('load');
		$resultMethod->setStatic(true);
		$resultMethod->setVisibility(Zend_CodeGenerator_Php_Property::VISIBILITY_PUBLIC);
		
		//---------------------- Parameters ---------------------
		$paramZendDbObj = new Zend_CodeGenerator_Php_Parameter();
		$paramZendDbObj->setName('zendDbObj');
		$paramZendDbObj->setType('Zend_Db_Adapter_Abstract');
		$resultMethod->setParameter($paramZendDbObj);
		
		//Pkeys
		$pkeyAttributes = $this->getPersistenceInformation()->getPkeyAttributes();
		if(!empty($pkeyAttributes)){
			foreach($pkeyAttributes as $pkeyAttribute){
				/* @var $pkeyAttribute Zend_CodeGenerator_Php_Property */
				$paramPkey = new Zend_CodeGenerator_Php_Parameter();
				$paramPkey->setName('par'.ucfirst($pkeyAttribute->getName()));
				$paramPkey->setType(Model_ClassGenerator_PersistenceInformation::getType($pkeyAttribute));
				$resultMethod->setParameter($paramPkey);
			}
		}		
		//--------------------------------------------------------
		//Pkeys
		$body = 'if(!isset('.$this->_provideInstancesKeySelectorString().')){'."\n";
		
		$docblock = new Zend_CodeGenerator_Php_Docblock();
		$docblock->setShortDescription('Loads the entry from the database.');
		$resultMethod->setDocblock($docblock);
		
		$body .= "\t".'$table = self::'.$this->_provideTableConstant().';'."\n"; //TODO - Konstante!
		$body .= "\t".'$select = $this->zendDbObj->select($table, \'*\');'."\n";
		$pkeyAttributes = $this->getPersistenceInformation()->getPkeyAttributes();
		if(!empty($pkeyAttributes)){
			foreach($pkeyAttributes as $pkeyAttribute){
				/* @var $pkeyAttribute Zend_CodeGenerator_Php_Property */
				$body .= "\t".'$select->where(self::'.$this->_provideTableConstant().'.\'.\'.self::'.$this->_provideColumnConstant($pkeyAttribute).'.\'=?\', $this->'.$pkeyAttribute->getName().');'."\n\n";;
			}
		}		
		$body .= "\t".'self::$instance'.$keysString.' = new self($parZendDbObj);'."\n";		
		$body .= '}'."\n\n";		
		$body .= 'return self::$instance'.$keysString.';';
		$resultMethod->setBody($body);
		
		$this->getClass()->setMethod($resultMethod);
	}

	/**
	 * @see models_ClassGenerator_Defaults_Interface::createMethodDelete
	 */
	public function createMethodDelete(){
		$resultMethod = new Zend_CodeGenerator_Php_Method();
		$resultMethod->setName('delete');
		$resultMethod->setVisibility(Zend_CodeGenerator_Php_Property::VISIBILITY_PUBLIC);
		
		$body = '$table = self::'.$this->_provideTableConstant().';'."\n"; //TODO - Konstante!
		$pkeyAttributes = $this->getPersistenceInformation()->getPkeyAttributes();
		if(!empty($pkeyAttributes)){
			foreach($pkeyAttributes as $pkeyAttribute){
				/* @var $pkeyAttribute Zend_CodeGenerator_Php_Property */
				$body .= '$where[] = $this->zendDbObj->quoteInto(self::'.$this->_provideTableConstant().'.\'.\'.self::'.$this->_provideColumnConstant($pkeyAttribute);
				$body .= '.\'=?\', $this->'.$pkeyAttribute->getName().');'."\n";
			}
		}		
		$body .= '$rowsAffected = $this->zendDbObj->delete($table, $where);'."\n";
		$body .= 'if($rowsAffected<>1){'."\n";
		$body .= "\t".'throw new Exception(\'Delete not successful. "\'.$rowsAffected.\'" rows were deleted.\');'."\n";
		$body .= '}'."\n";
		
		$body .= 'unset('.$this->_provideInstancesKeySelectorString().');';
		
		$resultMethod->setBody($body);
		
		//TODO - Autoincrement-Felder leeren		
		$docblock = new Zend_CodeGenerator_Php_Docblock();
		$docblock->setShortDescription('Deletes the entry from the database and clears the automatically set values.');
		$resultMethod->setDocblock($docblock);
		
		$this->getClass()->setMethod($resultMethod);
	}
	
	/**
	 * @see models_ClassGenerator_Defaults_Interface::createMethodIsSaved
	 */
	public function createMethodIsSaved(){
		$resultMethod = new Zend_CodeGenerator_Php_Method();
		$resultMethod->setName('save');
		$resultMethod->setVisibility(Zend_CodeGenerator_Php_Property::VISIBILITY_PUBLIC);
		
		$saArray = $this->getPersistenceInformation()->getSerialAttributes();
		if(!empty($saArray)){
			foreach($saArray as $serialAttribute){
				/* @var $serialAttribute Zend_CodeGenerator_Php_Property */
				$body .= 'if($this->$'.$serialAttribute->getName().'!== null){'."\n";
				$body .= "\t".'return false;'."\n";
				$body .= '}'."\n";
			}
		}		
		$body .= 'return true;';
		$resultMethod->setBody($body);
		
		$docblock = new Zend_CodeGenerator_Php_Docblock();
		$docblock->setShortDescription('Returns true if the object is saved (must not be current state) persistent in the database.');
		$docblock->setTags(array('return', Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_BOOL));
		$resultMethod->setDocblock($docblock);
		
		$this->getClass()->setMethod($resultMethod);
	}
	
	/**
	 * @see models_ClassGenerator_Defaults_Interface::createMethodSave
	 */
	public function createMethodSave(){
		$resultMethod = new Zend_CodeGenerator_Php_Method();
		$resultMethod->setName('save');
		$resultMethod->setVisibility(Zend_CodeGenerator_Php_Property::VISIBILITY_PUBLIC);
				
		$body = 'if($this->isSaved()) {'."\n";
		$body .= "\t".'$this->saveUpdate();'."\n";
		$body .= '} else {'."\n";
		$body .= "\t".'$this->saveNew();'."\n";
		$body .= '}';
		$resultMethod->setBody($body);
		
		//Comment
		$docblock = new Zend_CodeGenerator_Php_Docblock();
		$docblock->setLongDescription('Persists the object to the database, creates a new entry if not persistet yet, else updates the existing entry.');
		$resultMethod->setDocblock($docblock);
		
		$this->getClass()->setMethod($resultMethod);
	}
	
	/**
	 * @see models_ClassGenerator_Defaults_Interface::createMethodSaveNew
	 */
	public function createMethodSaveNew(){
		$resultMethod = new Zend_CodeGenerator_Php_Method();
		$resultMethod->setName('saveNew');
		$resultMethod->setVisibility(Zend_CodeGenerator_Php_Property::VISIBILITY_PROTECTED);
		
		$body = '$table = self::'.$this->_provideTableConstant().';'."\n"; //TODO - Konstante!
		$body .= '$data = $this->toArray();'."\n";
		$body .= '$rowsAffected = $this->zendDbObj->insert($table, $data);'."\n";
		
		$body .= 'if($rowsAffected<>1){'."\n";
		$body .= "\t".'throw new Exception(\'Save new not successful. "\'.$rowsAffected.\'" rows were inserted.\');'."\n";
		$body .= '}'."\n";
		$body .= '//Save to instances cache.'."\n";
		$body .= $this->_provideInstancesKeySelectorString().' = $this;';
		
		$resultMethod->setBody($body);
		
		//Comment
		$docblock = new Zend_CodeGenerator_Php_Docblock();
		$docblock->setLongDescription('Creates a new database entry representing this objects persistence.');
		$resultMethod->setDocblock($docblock);
		
		$this->getClass()->setMethod($resultMethod);
	}
	
	/**
	 * @see models_ClassGenerator_Defaults_Interface::createMethodSaveUpdate
	 */
	public function createMethodSaveUpdate(){
		$resultMethod = new Zend_CodeGenerator_Php_Method();
		$resultMethod->setName('saveUpdate');
		$resultMethod->setVisibility(Zend_CodeGenerator_Php_Property::VISIBILITY_PROTECTED);
		
		$body = '$table = self::'.$this->_provideTableConstant().';'."\n"; //TODO - Konstante!
		$body .= '$data = $this->toArray();'."\n";
		$pkeyAttributes = $this->getPersistenceInformation()->getPkeyAttributes();
		if(!empty($pkeyAttributes)){
			foreach($pkeyAttributes as $pkeyAttribute){
				/* @var $pkeyAttribute Zend_CodeGenerator_Php_Property */
				$body .= '$where = $this->zendDbObj->quoteInto(self::'.$this->_provideTableConstant().'.\'.\'.self::'.$this->_provideColumnConstant($pkeyAttribute);
				$body .= '.\'=?\', $this->'.$pkeyAttribute->getName().');'."\n";
			}
		}	
		$body .= '$rowsAffected = $this->zendDbObj->update($table, $data, $where);'."\n";
		$body .= 'if($rowsAffected<>1){'."\n";
		$body .= "\t".'throw new Exception(\'Save new not successful. "\'.$rowsAffected.\'" rows were inserted.\');'."\n";
		$body .= '}';
		
		$resultMethod->setBody($body);
		
		//Comment
		$docblock = new Zend_CodeGenerator_Php_Docblock();
		$docblock->setLongDescription('Updates the database entry containing the persistence data of this object.');
		$resultMethod->setDocblock($docblock);
		
		$this->getClass()->setMethod($resultMethod);
	}
	
	/**
	 * @see models_ClassGenerator_Defaults_Interface::createMethodToArray
	 */
	public function createMethodToArray(){
		$resultMethod = new Zend_CodeGenerator_Php_Method();
		$resultMethod->setName('toArray');
		$resultMethod->setVisibility(Zend_CodeGenerator_Php_Property::VISIBILITY_PUBLIC);
		
		$body = 'return array('."\n";
		$columnConstants = $this->_provideSqlColumnConstants();
		if(!empty($columnConstants)){
				foreach($columnConstants as $columnConstant => $attribute){
				/* @var $catp Zend_CodeGenerator_Php_Property */
				//TODO - Später in Konstante!
				$body .= "\t".'self::'.$columnConstant.'\' => $this->'.$attribute->getName().",\n"; 			
			}
		}
		$body .=');';
		$resultMethod->setBody($body);
		
		//Comment
		$docblock = new Zend_CodeGenerator_Php_Docblock();
		$docblock->setLongDescription('Returns the array representation of the object using the table column names as keys.');
		$resultMethod->setDocblock($docblock);
		
		$this->getClass()->setMethod($resultMethod);
	}
	
	/**
	 * @see models_ClassGenerator_Defaults_Interface::createSqlCreateTable
	 */
	public function createSqlCreateTable(){
		$sql .= "\n".'---- SQL CREATE TABLE STATEMENT: ----'."\n";
		$sql .= 'CREATE TABLE '.$this->getPersistenceInformation()->getTableName();
		$sql .= "(\n";
		$attributesToPersist = $this->getPersistenceInformation()->getAttributesToPersist();
		if(!empty($attributesToPersist)){
			foreach($attributesToPersist as $attribute){				
				$sql .= "\t".Model_ClassGenerator_PersistenceInformation::toColumnName($attribute);
				$sql .= ' ';
				$sql .= Model_ClassGenerator_PersistenceInformation::getSqlType($attribute);
				$sql .= ",\n";
			}	
		}
		$sql .= ');';
		$sql .= "\n".'-------------------------------------'."\n";
		
		$classDocLong = $this->getClass()->getDocblock()->getLongDescription();
		$this->getClass()->getDocblock()->setLongDescription($classDocLong."\n".$sql."\n");
	}
	
	/**
	 * @see models_ClassGenerator_Defaults_Interface::createAttributesSqlConstants
	 */
	public function createAttributesSqlConstants(){
		//Table
		$constTable = new Zend_CodeGenerator_Php_Property();
		$constTable->setConst(true);
		$constTable->setName($this->_provideTableConstant());
		$constTable->setDefaultValue($this->getPersistenceInformation()->getTableName());
		$tableDocblock = new Zend_CodeGenerator_Php_Docblock();
		$tableDocblock->setLongDescription('The SQL table to persist all properties to.');
		$tableTagType = new Zend_CodeGenerator_Php_Docblock_Tag();
		$tableTagType->setName('var');
		$tableTagType->setDescription(Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_STRING);
		$tableDocblock->setTag($tableTagType);
		$constTable->setDocblock($tableDocblock);

		//Einfügen!		
		$this->getClass()->setProperty($constTable);
		
		//Columns
		$columnConstants = $this->_provideSqlColumnConstants();
		if(!empty($columnConstants)){
			foreach($columnConstants as $columnConstant => $attribute){
				/* @var $attribute Zend_CodeGenerator_Php_Property */
				$constCol = new Zend_CodeGenerator_Php_Property();
				$constCol->setConst(true);				
				$constCol->setName($columnConstant);
				$constCol->setDefaultValue(Model_ClassGenerator_PersistenceInformation::toColumnName($attribute));
				$colDocblock = new Zend_CodeGenerator_Php_Docblock();
				$colDocblock->setLongDescription('The SQL table colum to persist the attribute $'.$attribute->getName().' to.');
				$colTagType = new Zend_CodeGenerator_Php_Docblock_Tag();
				$colTagType->setName('var');
				$colTagType->setDescription(Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_STRING);
				$colDocblock->setTag($colTagType);
				$constCol->setDocblock($colDocblock);
				
				//Einfügen!		
				$this->getClass()->setProperty($constCol);
			}
		}			
	}
	
	protected function _provideInstancesKeySelectorString(){
		$keysString = '';
		$pkeyAttributes = $this->getPersistenceInformation()->getPkeyAttributes();
		if(!empty($pkeyAttributes)){
			foreach($pkeyAttributes as $pkeyAttribute){
				/* @var $pkeyAttribute Zend_CodeGenerator_Php_Property */
				$keysString .= '['.'$par'.ucfirst($pkeyAttribute->getName()).']';
			}
		} else {
			throw new Exception('No primary keys given.');
		}
		return 'self::$instance'.$keysString;
	}
	
	/**
	 * @return array
	 */
	protected function _provideSqlColumnConstants(){
		$result = array();
		$tableName = $this->getPersistenceInformation()->getTableName();
		$tableNameUpper = strtoupper($tableName);
		$attributesToPersist = $this->getPersistenceInformation()->getAttributesToPersist();
		if(!empty($attributesToPersist)){
			foreach($attributesToPersist as $attribute){
				/* @var $attribute Zend_CodeGenerator_Php_Property */	
				$columnName = strtoupper(Model_ClassGenerator_PersistenceInformation::toColumnName($attribute));
				$result[$this->_provideColumnConstant($attribute)] = $attribute;
			}
		}
		
		return $result;
	}
	
	/**
	 * @return string
	 */
	protected function _provideColumnConstant(Zend_CodeGenerator_Php_Property $parAttribute){
		$tableName = $this->getPersistenceInformation()->getTableName();
		$tableNameUpper = strtoupper($tableName);
		$columnName = strtoupper($parAttribute->getName());
		return 'DB_COL_'.$tableNameUpper.'_'.strtoupper($columnName);
	}
	
	/**
	 * @return string
	 */
	protected function _provideTableConstant(){
		$tableName = $this->getPersistenceInformation()->getTableName();
		$tableNameUpper = strtoupper($tableName);
		return 'DB_TABLE_'.$tableNameUpper;
	}
	
	/**
	 * Returns the persistence information container.
	 * 
	 * @return Model_ClassGenerator_PersistenceInformation
	 */
	public function getPersistenceInformation(){
		return $this->persistenceInformation;
	}
	
	/**
	 * Returns the class to manipulate.
	 * 
	 * @return Zend_CodeGenerator_Php_Class
	 */
	public function getClass(){
		return $this->class;
	}
	
	/**
	 * Sets the class to manipulate.
	 * 
	 * @param Zend_CodeGenerator_Php_Class $class
	 */
	public function setClass(Zend_CodeGenerator_Php_Class $class){
		$this->class = $class;
	}
}

?>