<?php

namespace Vibius\Router;

use Container, Exception, RequestParser;

class Router{

	use \Vibius\Facade\Utils;

	public function __construct(){
		
		$this->container = Container::open('Router');
		$this->alternatives = Container::open('Router.alternatives');
	
	}

	public function has($uri){
		if( $this->container->exists($uri) ){
			return true;
		}
	}

	public function add($uri, $callback, $type = 'GET'){

		if( $this->has($uri) ){
			throw new Exception("Route ($uri) already exists");
		}

		if( !is_callable($callback) ){
			throw new Exception("Callback of route ($uri) must be callable, not ".gettype($callback));
		}

		$this->container->add($uri, [
			'uri' => $uri,
			'type' => $type,
			'callback' => $callback,
			'before' => function(){},
			'after' => function(){},
			'parameters' => []
		]);

		$this->last = $uri;

		return $this;
	}

	public function edit($uri, $properties){

		if( !$this->has($uri) ){
			throw new Exception("Route ($uri) does not exist");
		}

		$result = array_merge( $this->container->get($uri), $properties );

		$this->container->override($uri, $result);

		$this->last = $uri;

		return $this;
	}

	public function override($uri, $callback, $type = 'GET'){
		
		if( !$this->has($uri) ){
			throw new Exception("Route ($uri) does not exist");
		}

		$this->container->override($uri, [
			'uri' => $uri,
			'type' => $type,
			'callback' => $callback,
			'before' => function(){},
			'after' => function(){},
			'parameters' => []

		]);

		$this->last = $uri;

		return $this;
	}

	public function before($action){
		
		if( !is_callable($action) ){
			throw new Exception("Before action of route ($uri) must be callable, not ".gettype($callback));
		}

		$route = $this->container->get( $this->last );
		$route['before'] = $action;

		$this->container->override( $this->last, $route );

		return $this;

	}

	public function after($action){

		if( !is_callable($action) ){
			throw new Exception("After action of route ($uri) must be callable, not ".gettype($callback));
		}

		$route = $this->container->get( $this->last );
		$route['after'] = $action;

		$this->container->override( $this->last, $route );

		return $this;

	}

	public function parameters($parameters){

		if( !is_array($parameters) ){
			throw new Exception("Parameters must be in form of array");
		}

		$route = $this->container->get( $this->last );
		$route['parameters'] = $parameters;

		return $this;
	}

	public function alternatives(){
		return $this->alternatives;
	}

	public function getDefinedRoutes(){
		return $this->container->storage;
	}

	public function dispatch($request = []){

		return RequestParser::parse($request);

	}

	public function execute($actions){
		
		$actions['before']();

		$actions['callback']();

		$actions['after']();
	}
}
