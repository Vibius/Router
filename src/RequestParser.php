<?php

namespace Vibius\Router;

use Request, Exception, stdClass;

class RequestParser{

	public function parse($data){

		$this->data = [
			'uri' => Request::get('data.uri'),
			'post' => Request::get('data.post'),
			'get' => Request::get('data.get'),
			'type' => 'GET'
		];

		if( !empty($data) ){
			if( empty($data['uri']) ){
				throw new Exception('Modified request requires valid URI');
			}
			$this->data = array_merge($this->data, $data);
		}

		$this->response = [];

		$this->response = $this->getFulltextMatch();
		
		if( empty($this->response) ){
			$this->response = $this->getRegexMatch();
		}

		return $this->response;
	}

	public function getFulltextMatch(){
		foreach (\Router::getDefinedRoutes() as $key => $value) {
			if( $key == $this->data['uri'] ){
				return $value;
			}
		}
	}

	public function getRegexMatch(){
		foreach (\Router::getDefinedRoutes() as $key => $value) {
			if( $key == $this->data['uri'] ){
				return $value;
			}
		}
	}

}