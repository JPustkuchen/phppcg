<?php

class Form_ClassGenerator_Attributes extends Zend_Form_SubForm 
{ 
    public function __construct($options = null) 
    { 
        parent::__construct($options);                
        $this->setName('attributes');
		$this->setIsArray(true);
      				
       	$attributename = new Zend_Form_Element_Text(Model_ClassGenerator_FormToClass::$nameKey);
        $attributename->setLabel('Name')
             ->setRequired(true)->addValidator('NotEmpty', true);
             
        $attributeVisibility = new Zend_Form_Element_Select(Model_ClassGenerator_FormToClass::$visibilityKey);
        $attributeVisibility
        	->addMultiOption(Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PRIVATE, Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PRIVATE)
        	->addMultiOption(Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PROTECTED, Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PROTECTED)
        	->addMultiOption(Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PUBLIC, Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PUBLIC)	
        	->setLabel('Visibility')
            ->setRequired(true)->addValidator('NotEmpty', true);
        
        $attributeType = new Zend_Form_Element_Select(Model_ClassGenerator_FormToClass::$typeKey);
        $attributeType
        	->addMultiOption('', '---')
        	->addMultiOption(Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_ARRAY, Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_ARRAY)
        	->addMultiOption(Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_BOOL, Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_BOOL)
        	->addMultiOption(Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_FLOAT, Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_FLOAT)
        	->addMultiOption(Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_INTEGER, Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_INTEGER)
        	->addMultiOption(Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_STRING, Zend_CodeGenerator_Php_Property_DefaultValue::TYPE_STRING)
        	->addMultiOption('mixed', 'mixed')
        	->setLabel('Type');
             	        
        $attributeComment = new Zend_Form_Element_Textarea(Model_ClassGenerator_FormToClass::$commentKey);
        $attributeComment
        	->setLabel('Comment')
	        ->setAttrib('cols', '50')
	    	->setAttrib('rows', '4');
        
        $attributeColumn = new Zend_Form_Element_Text(Model_ClassGenerator_FormToClass::$columnKey);
        $attributeColumn->setLabel('Column');	
        
        $attributeColumnSerial = new Zend_Form_Element_Checkbox(Model_ClassGenerator_FormToClass::$serialKey);
        $attributeColumnSerial->setLabel('Is serial');	
        
        $attributeColumnPkey = new Zend_Form_Element_Checkbox(Model_ClassGenerator_FormToClass::$pkeyKey);
        $attributeColumnPkey->setLabel('Is primary key');	
        
        $this->addElements(array(
        	$attributename,
            $attributeVisibility,
            $attributeType,
            $attributeComment,
            $attributeColumn,
            $attributeColumnSerial,
            $attributeColumnPkey
        ));
    } 
}