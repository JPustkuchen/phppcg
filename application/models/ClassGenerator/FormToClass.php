<?php

/**
 * Transforms parameters from a Persistence Form to a Class.
 * 
 * TODO: ArrayToClass als Subvariante (Hintergeschaltet)?
 * 
 * @author Julian Pustkuchen
 * @since 170710
 *
 */
class Model_ClassGenerator_FormToClass  {

	//Class
	public static $classKey 			= 'class';
	public static $attributesKey		= 'attributes';
		
	//Persistence
	public static $withPersistenceKey	= 'withPersistence';
	public static $tableKey 			= 'table';
	public static $columnKey			= 'column';
	public static $serialKey			= 'serial';
	public static $pkeyKey				= 'pkey';
	
	//Common
	public static $commentKey 			= 'comments';
	public static $nameKey				= 'name';
	public static $visibilityKey 		= 'visibility';
	public static $typeKey 				= 'type';
	
	/**
	 * The Form Data Array.
	 * 
	 * @var array
	 */
	private $formData;

	/**
	 * Creates a new FormToClass instance.
	 * 
	 * @param array $formData
	 * @return FormToClass
	 */
	public static function createInstance(array $formData){
		return new self($formData);
	}
	
	private function __construct(array $formData) {
		$this->formData = $formData;
	}
	
	/**
	 * Returns the class values assigned by their keys.
	 * 
	 * @return array
	 */
	public function getClassValues(){
		return $this->formData[self::$classKey];
	}	
	
	/**
	 * Returns the class name.
	 * 
	 * @return string
	 */
	public function getClassName(){
		$class = $this->getClassValues();		
		return $class[self::$nameKey];
	}
	
	/**
	 * Returns the class Comment.
	 * 
	 * @return string
	 */
	public function getClassComments(){
		$class = self::getClassValues();
		return $class[self::$commentKey];
	}
	
	/**
	 * Returns an array of all the attributes values assigned by their keys.
	 * 
	 * @return array
	 */
	public function getAttributesValues(){
		//TODO - Hier vermutlich Mapping nötig.
		return array($this->formData[self::$classKey][self::$attributesKey]);
	}
	
	/**
	 * Returns an array of the attributes names assigned by their keys.
	 * 
	 * @return array
	 */
	public function getAttributesNames(){
		$attributes = $this->getAttributesValues();
		
		$comments = array();
		if(!empty($attributes)){
			foreach($attributes as $key => $attributeProperties){
				$comments[$key] = $attributeProperties[self::$nameKey];
			}
		}
		
		return $comments;
	}

	/**
	 * Returns an array of the attributes comments assigned by their keys.
	 * 
	 * @return array
	 */
	public function getAttributesComments(){
		$attributes = $this->getAttributesValues();
		
		$comments = array();
		if(!empty($attributes)){
			foreach($attributes as $key => $attributeProperties){
				$comments[$key] = $attributeProperties[self::$commentKey];
			}
		}
		
		return $comments;
	}
	
	/**
	 * Returns an array of the attributes visibilities assigned by their keys.
	 * 
	 * @return array
	 */
	public function getAttributesVisibilities(){
		$attributes = $this->getAttributesValues();
		
		$comments = array();
		if(!empty($attributes)){
			foreach($attributes as $key => $attributeProperties){
				$comments[$key] = $attributeProperties[self::$visibilityKey];
			}
		}
		
		return $comments;
	}
		
	/**
	 * Generates the Class
	 * 
	 * @return Zend_CodeGenerator_Php_Class
	 */
	public function _generateClass(){
		//Generate Class
		$class = new Zend_CodeGenerator_Php_Class();
		
		$class->setName($this->getClassName());
		$classComment = $this->getClassComments();
		if(!empty($classComment)){
			$classDocblock = new Zend_CodeGenerator_Php_Docblock(array(
				'longDescription'	=>	$classComment	//TODO - Später ggf. in Long + Short unterteilen
			));
			$class->setDocblock($classDocblock);
		}
		
		//Persistence
		$classTable = self::extractTable($this->getClassValues());
		if(!empty($classTable)){
			$tag = Zend_CodeGenerator_Php_Docblock_Tag::factory(Model_ClassGenerator_Persistence::$tagTable);
			$tag->setDescription($classTable);
			if(!$class->getDocblock()){
				$class->setDocblock(new Zend_CodeGenerator_Php_Docblock());
			}
			$class->getDocblock()->setTag($tag);
		}
		
		return $class;
	}
	
	/**
	 * Generates the attributes of the class.
	 * 
	 * @return Zend_CodeGenerator_Php_Property
	 */
	public function _generateAttributes(){
		$attributes = $this->getAttributesValues();
		$properties = array();
		if(!empty($attributes)){
			foreach($attributes as $key => $attributeProperties){
				$property = new Zend_CodeGenerator_Php_Property();
				//Name
				$property->setName(self::extractName($attributeProperties));
				
				//Visibility
				$property->setVisibility(self::extractVisibility($attributeProperties));
				
				//DocBlock
				$propertyDocblock = new Zend_CodeGenerator_Php_Docblock();
				$docblockSet = false;
				
				//Type
				$attributeType = self::extractType($attributeProperties);
				if(!empty($attributeType)){
					$tag = Zend_CodeGenerator_Php_Docblock_Tag::factory('var');
					$tag->setDescription($attributeType);
					$propertyDocblock->setTag($tag);
					$docblockSet = true;
				}
				
				//Persistence: Column
				$attributeColumn = self::extractColumn($attributeProperties);
				if(!empty($attributeColumn)){
					$tag = Zend_CodeGenerator_Php_Docblock_Tag::factory(Model_ClassGenerator_Persistence::$tagColumn);
					$tag->setDescription($attributeColumn);
					$propertyDocblock->setTag($tag);
					$docblockSet = true;
				}
				
				//Persistence: Serial
				$attributeColumnIsSerial = self::extractIsSerial($attributeProperties);
				if(!empty($attributeColumnIsSerial)){
					$tag = Zend_CodeGenerator_Php_Docblock_Tag::factory(Model_ClassGenerator_Persistence::$tagSerial);
					$propertyDocblock->setTag($tag);
					$docblockSet = true;
				}
				
				//Persistence: Primary Key
				$attributeColumnIsPkey = self::extractIsPkey($attributeProperties);
				if(!empty($attributeColumnIsPkey)){
					$tag = Zend_CodeGenerator_Php_Docblock_Tag::factory(Model_ClassGenerator_Persistence::$tagPkey);
					$propertyDocblock->setTag($tag);
					$docblockSet = true;
				}
				
				//Comment
				$attributesComment = self::extractComment($attributeProperties);				
				if(!empty($attributesComment)){
					$propertyDocblock->setLongDescription($attributesComment);
					$docblockSet = true;
				}
				
				//Set Docblock if it has content.
				if($docblockSet){
					$property->setDocblock($propertyDocblock);
				}
				
				
				$properties[] = $property;
			}
		}
		return $properties;
	}
	
	/**
	 * Returns the generated Zend_CodeGenerator_Php_Class with all the properties applied.
	 * 
	 * @return Zend_CodeGenerator_Php_Class
	 */
	public function toClass(){
		$class = $this->_generateClass();		
		$attributes = $this->_generateAttributes();
		$class->setProperties($attributes);

		return $class;
	}
	
	/**
	 * Extracts the name from the given Array by self::$nameKey.
	 * 
	 * @param array $parContainerArray 
	 * @return string
	 */
	public static function extractName($parContainerArray){
		return $parContainerArray[self::$nameKey];
	}
	
	/**
	 * Extracts the comment from the given Array by self::$commentKey.
	 * 
	 * @param array $parContainerArray 
	 * @return string
	 */
	public static function extractComment($parContainerArray){
		return $parContainerArray[self::$commentKey];
	}
	
	/**
	 * Extracts the visibility from the given Array by self::$typeKey.
	 * 
	 * @param array $parContainerArray 
	 * @return string
	 */
	public static function extractVisibility($parContainerArray){
		return $parContainerArray[self::$visibilityKey];
	}
	
	/**
	 * Extracts the type from the given Array by self::$typeKey.
	 * 
	 * @param array $parContainerArray 
	 * @return string
	 */
	public static function extractType($parContainerArray){
		return $parContainerArray[self::$typeKey];
	}
	
	/**
	 * Extracts the column from the given Array by self::$columnKey.
	 * 
	 * @param array $parContainerArray 
	 * @return string
	 */
	public static function extractColumn($parContainerArray){
		return $parContainerArray[self::$columnKey];
	}
	
	/**
	 * Extracts the table from the given Array by self::$tableKey.
	 * 
	 * @param array $parContainerArray 
	 * @return string
	 */
	public static function extractTable($parContainerArray){
		return $parContainerArray[self::$tableKey];
	}
	
	/**
	 * Extracts the "is serial" from the given Array by self::$serialKey.
	 * 
	 * @param array $parContainerArray 
	 * @return string
	 */
	public static function extractIsSerial($parContainerArray){
		return $parContainerArray[self::$serialKey];
	}
	
	/**
	 * Extracts the "is primary key" from the given Array by self::$pkeyKey.
	 * 
	 * @param array $parContainerArray 
	 * @return string
	 */
	public static function extractIsPkey($parContainerArray){
		return $parContainerArray[self::$pkeyKey];
	}
}
?>