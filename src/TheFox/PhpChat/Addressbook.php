<?php

namespace TheFox\PhpChat;

use TheFox\Yaml\YamlStorage;

class Addressbook extends YamlStorage{
	
	private $contactsId = 0;
	private $contacts = array();
	private $contactsByNodeId = array();
	
	public function __construct($filePath = null){
		#print __CLASS__.'->'.__FUNCTION__.''."\n";
		parent::__construct($filePath);
		
		$this->data['timeCreated'] = time();
	}
	
	public function __sleep(){
		return array('contacts');
	}
	
	public function save(){
		#print __CLASS__.'->'.__FUNCTION__.''."\n";
		
		$this->data['contacts'] = array();
		foreach($this->contacts as $contactId => $contact){
			
			$objAr = array();
			$objAr['nodeId'] = $contact->getNodeId();
			$objAr['userNickname'] = $contact->getUserNickname();
			$objAr['timeCreated'] = $contact->getTimeCreated();
			
			$this->data['contacts'][] = $objAr;
		}
		
		$rv = parent::save();
		unset($this->data['contacts']);
		
		return $rv;
	}
	
	public function load(){
		if(parent::load()){
			
			if(isset($this->data['contacts']) && $this->data['contacts']){
				foreach($this->data['contacts'] as $contactId => $contactAr){
					$this->contactsId = $contactId;
					
					$contact = new Contact();
					$contact->setId($this->contactsId);
					$contact->setNodeId($contactAr['nodeId']);
					$contact->setUserNickname($contactAr['userNickname']);
					$contact->setTimeCreated($contactAr['timeCreated']);
					
					$this->contacts[$contact->getId()] = $contact;
					$this->contactsByNodeId[$contact->getNodeId()] = $contact;
				}
			}
			unset($this->data['contacts']);
			
			return true;
		}
		
		return false;
	}
	
	public function contactAdd(Contact $contact){
		$ocontact = $this->contactGetByNodeId($contact->getNodeId());
		if(!$ocontact){
			$this->contactsId = $this->contactsId + 1;
			
			$contact->setId($this->contactsId);
			
			$this->contacts[$contact->getId()] = $contact;
			$this->contactsByNodeId[$contact->getNodeId()] = $contact;
		}
	}
	
	public function contactGetByNodeId($nodeId){
		if(isset($this->contactsByNodeId[$nodeId])){
			return $this->contactsByNodeId[$nodeId];
		}
		
		return null;
	}
	
	public function contactsGetByNick($userNickname){
		$contacts = array();
		foreach($this->contacts as $contactId => $contact){
			if(strtolower($contact->getUserNickname()) == strtolower($userNickname)){
				$contacts[] = $contact;
			}
		}
		return $contacts;
	}
	
	public function contactRemove($id){
		if(isset($this->contacts[$id])){
			$contact = $this->contacts[$id];
			if($contact){
				unset($this->contactsByNodeId[$contact->getNodeId()]);
				unset($this->contacts[$contact->getId()]);
				
				$this->setDataChanged(true);
				
				return true;
			}
		}
		
		return false;
	}
	
	public function getContacts(){
		return $this->contacts;
	}
	
}
