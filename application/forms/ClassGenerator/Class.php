<?php

require_once '../application/models/ClassGenerator/FormToClass.php';

class Form_ClassGenerator_Class extends Zend_Form 
{ 
    public function __construct($options = null) 
    { 
        parent::__construct($options);                
        $this->setName('class');
		$this->setIsArray(true);
        
        $classname = new Zend_Form_Element_Text(Model_ClassGenerator_FormToClass::$nameKey);
        $classname->setLabel('Class name')
        	->setRequired(true)->addValidator('NotEmpty', true);
        
        $classComment = new Zend_Form_Element_Textarea(Model_ClassGenerator_FormToClass::$commentKey);
        $classComment->setLabel('Class comment/description');
		$classComment
	        ->setAttrib('cols', '50')
    		->setAttrib('rows', '4');
        
        
        $persistenceOn = new Zend_Form_Element_Checkbox(Model_ClassGenerator_FormToClass::$withPersistenceKey);
        $persistenceOn->setLabel('Persistence ON');
                
        $table = new Zend_Form_Element_Text(Model_ClassGenerator_FormToClass::$tableKey);
        $table->setLabel('Database Table');
        
        $addFieldDynId = new Zend_Form_Element_Hidden('addFieldDynId');
        $addFieldDynId->setAttrib('id', 'addFieldDynId');
        $addFieldDynId->setValue(1);
               
        $attributesSf = self::getAttributeSubform();        
        $this->addSubForm($attributesSf, 'attributes', 1);
        /*Use table decorator!
        require_once('../application/forms/Decorators/Table.php');
        $this->setDecorators(array(
			'FormElements',
			array('Table', array('doNotSetDecorators' => false)),
			'Form'
		));
        */
        $add = new Zend_Form_Element_Button('addAttribute');
        $add->setAttrib('id', 'addAttribute');
        $add->setLabel('Add Attribute');
        
        $remove = new Zend_Form_Element_Button('removeAttribute');
        $remove->setAttrib('id', 'removeAttribute');
        $remove->setLabel('Remove Attribute');
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Play!')->setOrder(5);
                        
        $this->addElements(array(
	        $classname,
	        $classComment,
	        $persistenceOn,
	        $table
        ));     

        $this->addDisplayGroup(array(
        	$classname->getName(),
        	$classComment->getName(),
        	$persistenceOn->getName(),
        	$table->getName(),
        	), 
        	'Class properties'
        )->setOrder(0);
        
        $this->addElements(array(	        
        	$add,
        	$remove,
            $submit,
            $addFieldDynId        	
        ));        
        
        $this->addElement('hash', 'no_csrf_foo', array('salt' => 'unique'));        
    }
    
    public static function getAttributeSubform(){
        $attributesSf = new Form_ClassGenerator_Attributes();
        $attributesSf->setLegend('Attribute');
        return $attributesSf;
    }
}