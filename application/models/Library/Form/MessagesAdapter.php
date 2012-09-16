<?php

class Model_Library_Form_MessagesAdapter {
	public static function getAdaptedMessagesArray(Zend_Form $zendFormObj){
		$messagesArray = $zendFormObj->getMessages();
	
		$adaptedMessagesObj = Model_Library_Messages_Message::createInstance(Model_Library_Messages_States_Error::createInstance());
		if(!empty($messagesArray)){
			foreach($messagesArray as $field => $messages){
				if(!empty($messages)){
					foreach($messages as $type => $message){
						$adaptedMessagesObj->addMessage($field.': '.$message);
					}
				}
			}
		}
		
		return $adaptedMessagesObj; 	
	}
}

?>