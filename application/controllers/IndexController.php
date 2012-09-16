<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('addattribute', 'html')->initContext();
    }

    public function indexAction()
    {       
        $this->_helper->layout()->title='PHP Persistent Class Generator';
        
       	$form = new Form_ClassGenerator_Class();
       	if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)) {
                $formToClass = Model_ClassGenerator_FormToClass::createInstance($formData);               
                $class = $formToClass->toClass();
                $file = new Zend_CodeGenerator_Php_File();
                if($formData['class'][Model_ClassGenerator_FormToClass::$withPersistenceKey]){
                	$persistence = Model_ClassGenerator_Persistence::createInstance($class);
                	$persistentClass = $persistence->createPersistence();
                	$file->setClass($persistentClass);
                } else {
                	$file->setClass($class);
                }                
				$this->view->resultCode = htmlentities($file->generate());
            } else {
                $form->populate($formData);
            }
        }

        $this->view->form = $form;
    }  
    
    public function addattributeAction(){
    	$id = $this->_getParam('id', null);
		
    	$attrSf = Form_ClassGenerator_Class::getAttributeSubform();
    	$this->view->field = $attrSf->__toString();   	
    }
}