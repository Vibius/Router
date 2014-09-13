<?php

namespace Vibius\Router;

use Server, Exception;

class RequestData extends \Vibius\Container\Container{

	use \Vibius\Container\Methods;
	
	function __construct(){

		$this->container = parent::open('RequestData');

		$appRoot = explode(Server::get('DOCUMENT_ROOT'), vibius_INDEXPATH)[1];
		$reqUri = explode($appRoot, Server::get('REDIRECT_URL'));

		if( !isset($reqUri[1]) ){
			$reqUri = '/';
		}else{
			$reqUri = $reqUri[1];
		}

		
		$this->container->add('data.request', $_REQUEST); 
		$this->container->add('data.get', $_GET); 
		$this->container->add('data.post', $_POST); 
		$this->container->add('data.uri', $reqUri);
	}

	public function postData($key){
		$post = $this->container->get('data.post');
		if( !isset($post[$key]) ){
			throw new Exception("Post key ($key) does not exist");
		}
		return $result;
	}

	public function getData($key){
		$get = $this->container->get('data.get');
		if( !isset($get[$key]) ){
			throw new Exception("Get key ($key) does not exist");
		}
		return $result;
	}

	public function requestData($key){
		$request = $this->container->get('data.request');
		if( !isset($get[$key]) ){
			throw new Exception("Request key ($key) does not exist");
		}
		return $result;
	}
}