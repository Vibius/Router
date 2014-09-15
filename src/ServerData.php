<?php

namespace Vibius\Router;

class ServerData extends \Vibius\Container\Container{

	use \Vibius\Container\Methods;

	public function __construct(){

		$this->container = parent::open('ServerData', true);

		foreach($_SERVER as $key => $value){
			$this->container->add($key, $value);
		}
	}

}