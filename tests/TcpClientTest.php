<?php

use TheFox\Dht\Kademlia\Node;
use TheFox\PhpChat\TcpClient;

class TcpClientTest extends PHPUnit_Framework_TestCase{
	
	public function testSerialize(){
		#fwrite(STDOUT, 'start'."\n");
		
		#fwrite(STDOUT, 'node'."\n");
		$node = new Node();
		$node->setIdHexStr('cafed00d-2131-4159-8e11-0b4dbadb1738');
		
		#fwrite(STDOUT, 'tcp client'."\n");
		$client = new TcpClient();
		$client->setId(21);
		$client->setUri('tcp://127.0.0.1:25000');
		$client->setNode($node);
		
		#fwrite(STDOUT, 'ser'."\n");
		$client = unserialize(serialize($client));
		#ve($client);
		
		$this->assertEquals(21, $client->getId());
		$this->assertEquals('tcp://127.0.0.1:25000', (string)$client->getUri());
		$this->assertEquals($node, $client->getNode());
		
		#fwrite(STDOUT, 'end'."\n");
	}
	
}
