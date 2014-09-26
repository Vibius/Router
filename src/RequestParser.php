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

		$this->alternatives = Container::open('Router.alternatives')->storage;

		return $this->getRegexMatch();
	}

	public function getRegexMatch(){

		$uri = $this->data['uri'];

		foreach (\Router::getDefinedRoutes() as $route => $routeContents) {
			$routeDetails = explode('%%%', $route);
			$routeUriRegex = preg_replace('(/)', '(\/)', $routeDetails[0]);

			foreach ( $this->alternatives as $alternative => $alternativeVal ){
				$pattern = '/('.$alternative.')/is';
				if( $c = preg_match_all($pattern,$routeUriRegex, $matches) ){
					$routeUriRegex = preg_replace($pattern, $alternativeVal, $routeUriRegex);
				}
			}
			
			if( preg_match('/^'.$routeUriRegex.'$/is', $uri) ){
				if( $routeDetails[1] == $this->data['type'] ){
					return $routeContents;
				}
			}
		}
		
	}


}