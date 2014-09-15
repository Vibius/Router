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

		$this->container->add($uri.'%%%'.$type, [
			'uri' => $uri,
			'type' => $type,
			'callback' => $callback,
			'before' => function(){},
			'after' => function(){},
			'aliases' => []
		]);

		$this->last = $uri;
		$this->lastType = $type;

		return $this;
	}

	public function alias($aliases){

		foreach($aliases as $uri => $type){
			
			if( empty($type) ){
				$type = 'GET';
			}

			if( $this->has($uri) ){
				throw new Exception("Route ($uri) already exists");
			}

			$aliasParent = $this->container->get("$this->last%%%$this->lastType");

			if( !isset($aliasParent['aliases'][$uri.'%%%'.$type]) ){
				array_push($aliasParent['aliases'], $uri.'%%%'.$type);
			}

			$this->container->override("$this->last%%%$this->lastType", $aliasParent);
			$this->container->add($uri.'%%%'.$type, $aliasParent);

		}

	}

	public function edit($uri, $type, $properties){

		if( !$this->has("$uri%%%$type") ){
			throw new Exception("Route ($uri) with type ($type) does not exist");
		}

		$result = array_merge( $this->container->get("$uri%%%$type"), $properties );

		foreach($result['aliases'] as $alias){
			$this->container->override($alias, $result);
		}

		$this->container->override("$uri%%%$type", $result);

		$this->last = $uri;

		return $this;
	}

	public function override($uri, $callback, $type = 'GET'){
		
		if( !$this->has("$uri%%%$type") ){
			throw new Exception("Route ($uri) does not exist");
		}

		$this->container->override("$uri%%%$type", [
			'uri' => $uri,
			'type' => $type,
			'callback' => $callback,
			'before' => function(){},
			'after' => function(){},
			'aliases' => []
		]);

		$this->last = $uri;

		return $this;
	}

	public function before($action){
		
		if( !is_callable($action) ){
			throw new Exception("Before action of route ($uri) must be callable, not ".gettype($callback));
		}

		$route = $this->container->get( "$this->last%%%$this->lastType" );
		$route['before'] = $action;

		$this->container->override( "$this->last%%%$this->lastType" , $route );

		return $this;

	}

	public function after($action){

		if( !is_callable($action) ){
			throw new Exception("After action of route ($uri) must be callable, not ".gettype($callback));
		}

		$route = $this->container->get( "$this->last%%%$this->lastType" );
		$route['after'] = $action;

		$this->container->override( "$this->last%%%$this->lastType" , $route );

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
