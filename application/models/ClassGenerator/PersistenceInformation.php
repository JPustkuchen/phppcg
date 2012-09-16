<?php

/**
 * Encapsulates the conditions and information for the persistence functionality.
 * This helper is used to check which of the persistence features can or have to be applied.
 * 
 * @author Julian
 *
 * TODO: 
 * - Datei Umbenennen wie Klasse.
 * - Setter-Funktionen mit Validierung
 */
class Model_ClassGenerator_PersistenceInformation  {
	/**
	 * List of attributes which should be persisted.
	 * 
	 * @var Zend_CodeGenerator_Php_Property[]
	 */
	private $attributesToPersist = array();
	
	/**
	 * The attributes that represent the serial values which are automatically incremented
	 * in the database. All of those are also contained in $attributesToPersist.
	 * 
	 * @var Zend_CodeGenerator_Php_Property[]
	 */
	private $serialAttributes = array();
	
	/**
	 * The attributes that represent the primary key in the database together.
	 * All of those are also contained in $attributesToPersist.
	 * 
	 * @var Zend_CodeGenerator_Php_Property[]
	 */
	private $pkeyAttributes = array();
	
	/**
	 * The name of the database table to persist in.
	 * 
	 * @var string
	 */
	private $tableName;
	
	//TODO - Insert your code here
	public static function createInstance(){
		return new self();
	}
	
	private function __construct(){
		
	}
	
	//------------- Serial Attributes ------------------------
	
	/**
	 * Adds an attributes which is marked as serial.
	 * 
	 * @param Zend_CodeGenerator_Php_Property $parSerialAttribute
	 */
	public function addSerialAttribute(Zend_CodeGenerator_Php_Property $parSerialAttribute){
		$this->serialAttributes[] = $parSerialAttribute;
	}
	
	/**
	 * @return Zend_CodeGenerator_Php_Property[]
	 */
	public function getSerialAttributes(){
		return $this->serialAttributes;
	}
	
	/**
	 * @return bool
	 */
	public function hasSerialAttribute(){
		return $this->getSerialAttributes() !== null;
	}
	
	//------------- Primary Key Attributes ------------------------
	
	/**
	 * Adds an attributes which is marked as pkey field.
	 * 
	 * @param Zend_CodeGenerator_Php_Property $parSerialAttribute
	 */
	public function addPkeyAttribute(Zend_CodeGenerator_Php_Property $parPkeyAttribute){
		$this->pkeyAttributes[] = $parPkeyAttribute;
	}
	
	/**
	 * @return Zend_CodeGenerator_Php_Property[]
	 */
	public function getPkeyAttributes(){
		return $this->pkeyAttributes;
	}
	
	/**
	 * @return bool
	 */
	public function hasPkeyAttribute(){
		return !empty($this->pkeyAttributes);
	}
	
	//-------------- Attributes to persist ----------------------------
	
	/**
	 * Adds an attribute to persist.
	 * 
	 * @param Zend_CodeGenerator_Php_Property $parAttribute
	 */
	public function addAttributeToPersist(Zend_CodeGenerator_Php_Property $parAttribute){
		$this->attributesToPersist[] = $parAttribute;
	}
	
	/**
	 * @return Zend_CodeGenerator_Php_Property[]
	 */
	public function getAttributesToPersist(){
		return $this->attributesToPersist;
	}
	
	/**
	 * @return bool
	 */
	public function hasAttributesToPersist(){
		return count($this->attributesToPersist) > 0;
	}
	
	//-------------- Table information --------------------------
	
	/**
	 * Sets the table information from the Table tag description string.
	 */
	public function setTableInformation($parTableTagDescription){
		$this->tableName = $parTableTagDescription;
		//TODO - Aus Text extrahieren: name=!
	}
	
	public function getTableName(){
		return $this->tableName;
	}
	
	public function hasTableName(){
		return $this->getTableName() !== null;
	}
	
	//--------------- Helper functions ------------------------
	
	/**
	 * Extracts the column name from an attribute. Uses the explicit name if given, else the 
	 * attributes name. 
	 * 
	 * @return string
	 */
	public static function toColumnName(Zend_CodeGenerator_Php_Property $parAttribute){
		$docblock = $parAttribute->getDocblock();
		if($docblock){
			$tags = $docblock->getTags();
			foreach($tags as $tag){
				/* @var $tag Zend_CodeGenerator_Php_Docblock_Tag */
				if($tag->getName() === Model_ClassGenerator_Persistence::$tagColumn){
					return $tag->getDescription();
				}
			}	
		} else {
			return implode('_', self::explodeCase($parAttribute->getName(), true));
		}		
	}
	
	/**
	 * Returns the type from the Attributes php tag type.
	 * 
	 * @return string
	 */
	public static function getType(Zend_CodeGenerator_Php_Property $parAttribute){
		$docblock = $parAttribute->getDocblock();
		if($docblock){
			$tags = $docblock->getTags();
			foreach($tags as $tag){
				/* @var $tag Zend_CodeGenerator_Php_Docblock_Tag */
				if($tag->getName() === 'var'){
					$type = $tag->getDescription();					
					return $type;
				}
			}			
		}
		
		return false;
	}
	
	/**
	 * Returns the SQL type from the Attributes php tag type.
	 * 
	 * @return string
	 */
	public static function getSqlType(Zend_CodeGenerator_Php_Property $parAttribute){
		$type = self::getType($parAttribute);
		return self::toSqlType($type);
	}
	
	public static function toSqlType($phpType){
		switch ($phpType) {
			case Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_ARRAY:
				return false;
			break;
			
			case Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_BOOL:
				return 'BOOLEAN';
			break;
			
			case Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_FLOAT:
				return 'FLOAT';
			break;
			
			case Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_INTEGER:
				return 'INTEGER';
			break;
			
			case Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_STRING:
				return 'VARCHAR(255)';
			break;
						
			default:
				return false;
			break;
		}
	}
	
	/**
	 * Splits up a string into an array similar to the explode() function but according to CamelCase.
	 * Uppercase characters are treated as the separator but returned as part of the respective array elements.
	 * @author Charl van Niekerk <charlvn@charlvn.za.net>
	 * @param string $string The original string
	 * @param bool $lower Should the uppercase characters be converted to lowercase in the resulting array?
	 * @return array The given string split up into an array according to the case of the individual characters.
	 */
	public static function explodeCase($string, $lower = true)
	{
	  // Initialise the array to be returned
	  $array = array();
	 
	  // Initialise a temporary string to hold the current array element before it's pushed onto the end of the array
	  $segment = '';
	 
	  // Loop through each character in the string
	  foreach (str_split($string) as $char) {
	    // If the current character is uppercase
	    if (ctype_upper($char)) {
	      // If the old segment is not empty (for when the original string starts with an uppercase character)
	      if ($segment) {
	        // Push the old segment onto the array
	        $array[] = $segment;
	      }
	     
	      // Set the character (either uppercase or lowercase) as the start of the new segment
	      $segment = $lower ? strtolower($char) : $char;
	    } else { // If the character is lowercase or special
	      // Add the character to the end of the current segment
	      $segment .= $char;
	    }
	  }
	 
	  // If the last segment exists (for when the original string is empty)
	  if ($segment) {
	    // Push it onto the array
	    $array[] = $segment;
	  }
	 
	  // Return the resulting array
	  return $array;
	}
	
}