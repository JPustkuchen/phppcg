<?php

/**
*	Repräsentiert eine Erfolg/Warn/Notizmeldung
*/
interface Model_Library_Messages_IMessage {
	public function toArray();
	public function addMessage($message);
	public function setMessages(array $messagesArray);
	public function clearMessages();
	public function getMessages();
	public function getState();
}

?>