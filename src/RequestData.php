<?php

namespace Vibius\Router;

use Server, Exception;

class RequestData extends \Vibius\Container\Container{

	use \Vibius\Container\Methods;
	
	function __construct(){

		$this->container = parent::open('RequestData', true, true);

		$this->postData = parent::open('POST', true, true);
		$this->getData = parent::open('GET', true, true);
		$this->requestData = parent::open('REQUEST', true, true);
		$this->cookieData = parent::open('COOKIE', true, true);
		$this->sessionData = parent::open('SESSION', true, true);

		$appRoot = explode(Server::get('DOCUMENT_ROOT'), vibius_INDEXPATH)[1];
		$reqUri = explode($appRoot, Server::get('REDIRECT_URL'));

		if( !isset($reqUri[1]) ){
			$reqUri = '/';
		}else{
			$reqUri = $reqUri[1];
		}

		foreach ($_GET as $key => $value) {
			$this->getData->add($key, $value);
		}

		foreach ($_POST as $key => $value) {
			$this->postData->add($key, $value);
		}

		foreach ($_REQUEST as $key => $value) {
			$this->requestData->add($key, $value);
		}

		foreach ($_COOKIE as $key => $value) {
			$this->requestData->add($key, $value);
		}

		$this->container->add('get', $this->getData);
		$this->container->add('post', $this->postData);
		$this->container->add('request', $this->requestData);
		$this->container->add('session', $this->sessionData);
		$this->container->add('cookie', $this->cookieData);

		$this->container->add('data.uri', $reqUri);
	}

	public function override($key, $value){
		throw new Exception("Request data cannot be overriden");
	}
}