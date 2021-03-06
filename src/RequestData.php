<?php

namespace Vibius\Router;

use Server, Exception;

class RequestData extends \Vibius\Container\Container{

	use \Vibius\Container\Methods;
	
	public function get($param){
		return $this->container->get('get')->get($param);
	}

	public function post($param){
		return $this->container->get('post')->get($param);
	}

	public function session($param){
		return $this->container->get('session')->get($param);
	}

	public function cookie($param){
		return $this->container->get('cookie')->get($param);
	}

	public function getUri(){
		return $this->uri;
	}

	public function segment($offset){
		return explode('/', $this->uri)[$offset];
	}

	public function segmentArray(){
		$arr = explode('/', $this->uri);
		array_shift($arr);
		return $arr;
	}

	function __construct(){

		$this->container = parent::open('RequestData', true, true);

		$this->postData = parent::open('POST', true, true);
		$this->getData = parent::open('GET', true, true);
		$this->requestData = parent::open('REQUEST', true, true);
		$this->cookieData = parent::open('COOKIE', true, true);
		$this->sessionData = parent::open('SESSION', true, true);

		if( !empty($_SERVER['REDIRECT_URL']) ){
			$appRoot = explode(Server::get('DOCUMENT_ROOT'), vibius_INDEXPATH);
			if( !empty($appRoot[1]) ){
				$appRoot = $appRoot[1];
				$reqUri = explode($appRoot, Server::get('REDIRECT_URL'));
			}else{
				$reqUri = Server::get('REDIRECT_URL');
			}

		}else{
			$reqUri = [];
		}


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
			$this->cookieData->add($key, $value);
		}

		$this->container->add('get', $this->getData);
		$this->container->add('post', $this->postData);
		$this->container->add('request', $this->requestData);
		$this->container->add('session', $this->sessionData);
		$this->container->add('cookie', $this->cookieData);

		$this->uri = $reqUri;
	}

	public function override($key, $value){
		throw new Exception("Request data cannot be overriden");
	}
}