<?php

/**
*	Repräsentiert eine Erfolg/Warn/Notizmeldung
*/
class Model_Library_Messages_Message implements Model_Library_Messages_IMessage, arrayaccess {
	
	protected $messages = array();
	protected $state;
	
	public static function createInstance(Model_Library_Messages_States_IState $messageState, array $messages=array()){
		return new self($messageState, $messages);
	}
	
	protected function __construct(Model_Library_Messages_States_IState $messageState, array $messages=array()){
		$this->setMessageState($messageState);
		$this->setMessages($messages);
	}
	
	public function addMessage($message){
		$this->messages[] = $message;
	}
	
	public function setMessages(array $messagesArray){
		$this->messages = $messagesArray;
	}
	
	public function clearMessages(){
		$this->setMessages(array());
	}
	
	public function getMessages(){
		return $this->messages;
	}
	
	public function getState(){
		return $this->state;
	}
	
	protected function setMessageState(Model_Library_Messages_States_IState $messageState){
		$this->state = $messageState;
	}
	
	public function toArray(){
		$result = array();
		$messages = $this->getMessages();
		if(!empty($messages)){
			foreach($messages as $message){
				$result[]['message'] = $message;	
			}
		}	
		
		return $result;
	}
	
	/* ----------- arrayaccess functionality --------------- */
	public function offsetSet($offset, $value) {
        $this->messages[$offset] = $value;
    }
    public function offsetExists($offset) {
        return isset($this->messages[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->messages[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->messages[$offset]) ? $this->messages[$offset] : null;
    }
}

?>