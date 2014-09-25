<?php

namespace Vibius\Router;

use Request, Exception, stdClass, Server, Container;

class RequestParser{

	public function parse($data){

		//request information
		$this->data = [
			'uri' => Request::getUri(),
			'type' => Server::get('REQUEST_METHOD')
		];

		//request simulation
		if( !empty($data) ){
			
			if( empty($data['uri']) ){
				throw new Exception('Modified request requires valid URI');
			}

			//edit request information
			$this->data = array_merge($this->data, $data);
		}

		//build up response
		$this->response = [];

		$this->response = $this->getFulltextMatch();

		if( empty($this->response) ){
			throw new Exception('Route not found');
		}

		return $this->response;
	}

	public function getFulltextMatch(){
		foreach (\Router::getDefinedRoutes() as $key => $value) {
			if( $key == $this->data['uri'].'%%%'.$this->data['type']){
				return $value;
			}
		}
	}

	public function getRegexMatch(){

		$this->alternatives = Container::open('Router.alternatives')->storage;

		foreach (\Router::getDefinedRoutes() as $key => $value) {
			$key = explode('%%%', $key)[0];
			
			
		}
	}

}