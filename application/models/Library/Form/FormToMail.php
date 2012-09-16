<?php

class Model_Library_Form_FormToMail {
	protected $form;
	protected $to;
	protected $toAlias; //TODO!
	protected $from;
	protected $fromAlias; //TODO!
	protected $selectedFields;
	
	protected $zendTextTable;
	
	public static function createInstance(Zend_Form $form){
		return new self($form);	
	}
	
	protected function __construct(Zend_Form $form){
		$this->setForm($form);
		$this->setSelectedFields($this->getAllFieldsArray());
		
		//TODO - Auslagern?
		Zend_Text_Table::setOutputCharset('ISO-8859-1');
		$this->setZendTextTable(new Zend_Text_Table(array('columnWidths' => array(20, 50))));
	}
	
	public function send(){
		$contentToSend=$this->getContentToSend();
		if(empty($contentToSend)){
			$contentToSend = '- keine -';
		}		
		
		$mail = new Zend_Mail();
		$mail->setSubject('Neue Nachricht aus einem Formular Ihrer Webseite.');
		$mail->setBodyText('Am '.date('d.m.Y - H:m:i')
			 				.' wurde ihnen das Formular mit den folgenden Angaben gesendet:'."\n\n"
			 				.$contentToSend
			 				.'Quelle: '.$_SERVER['REQUEST_URI']."\n"
			 				.'Sie erhalten diese E-Mail, da sie als Empfänger des Formulares eingetragen sind.')
			 ->setFrom($this->getFrom(), $this->getFrom());
			 
		$to = $this->getTo();
		if(!empty($to)){
			foreach($to as $alias => $recipient){
				if(!is_numeric($alias)){
					$mail->addTo($recipient, $alias);
				} else {
					$mail->addTo($recipient, $recipient);
				}
			}
		}				
		return $mail->send();
	}
	
	protected function getAllFieldsArray(){
		$form = $this->getForm();
		if(empty($form)){
			return array();
		} else {
			return array_keys($form->getValues());
		}
	}
	
	protected function getContentToSend(){
		$kontaktFormValues = $this->getForm()->getValues();
		
		$contentToSend = $this->getZendTextTable();
		
		if(!empty($kontaktFormValues)){
			$selectedFields = $this->getSelectedFields();
			if(empty($selectedFields)){
				//Alle Felder verwenden, wenn keine konkreten angegeben
				$selectedFields = $this->getAllFieldsArray();
			}
						
			foreach($selectedFields as $selectedField){
				if(isset($kontaktFormValues[$selectedField]) and !is_array($kontaktFormValues[$selectedField])){
					//Feld nur verwenden, wenn vorhanden
					$contentToSend->appendRow(array(strtoupper($selectedField), $kontaktFormValues[$selectedField]));
				}
			}
		}
		
		return $contentToSend;
	}
	
	protected function setForm(Zend_Form $form){
		$this->form = $form;
		return $this;
	}
	
	protected function getForm(){
		return $this->form;
	}
	
	public function setSelectedFields(array $parSelectedFieldsArray){
		$this->selectedFields = $parSelectedFieldsArray;
	}
	
	public function getSelectedFields(){
		return $this->selectedFields;	
	}
	
	protected function getEmailRecipient($receipientKeyOrMail){
		$allFieldArray = $this->getAllFieldsArray();
		if(isset($allFieldArray[$sender])){
			return $allFieldArray[$sender];			
		} else {
			$validator = new Zend_Validate_EmailAddress();
			if ($validator->isValid($receipientKeyOrMail)){
				return $receipientKeyOrMail;
			} else {
				throw new Exception('"'.$sender.'" is not an allowed key or email adress (as sender)');	
			}			
		}
	}
	
	protected function setToArray(array $recipients){
		$emailRecipientsArray = array();
		if(!empty($recipients)){
			foreach($recipients as $recipient){
				$emailRecipientsArray[] = $this->getEmailRecipient($recipient);
			}
		}
		$this->to = $emailRecipientsArray;
		return $this;
	}
	
	public function setTo($recipients){
		if(is_array($recipients)){
			$this->setToArray($recipients);
		} else {
			$this->setToArray(array($recipients));
		}
		return $this;
	}
	
	public function setFrom($sender){
		$this->from = $this->getEmailRecipient($sender);
		return $this;
	}
	
	public function getTo(){
		return $this->to;	
	}
	
	public function getToAlias(){
		return $this->toAlias;	
	}
	
	public function getFrom(){
		return $this->from;	
	}
	
	public function getFromAlias(){
		return $this->fromAlias;	
	}
	
	public function getZendTextTable(){
		return $this->zendTextTable;	
	}
	
	public function setZendTextTable(Zend_Text_Table $zendTextTable){
		$this->zendTextTable = $zendTextTable;
		
		return $this;
	}
}

?>