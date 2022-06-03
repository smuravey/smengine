<?php
class Front {

	private $controller, $action, $params = [];
	
	function __construct(){

		$parseUrl = parse_url( $_SERVER['REQUEST_URI'] );
		$this->path = explode( '/', trim( $parseUrl['path'], '/' ) );
		
		$this->controller = !empty($this->path[0]) ? ucfirst($this->path[0]) . 'Controller' : 'MainController';
		$this->action = !empty($this->path[1]) ? $this->path[1] . 'Action' : 'defaultAction';

		// $key = 2;

		// if( !empty($path[$key]) ) {
		// 	$this->params = array_slice($path, $key);
		// }

	}

	// function route() {

	// 	if( class_exists($this->controller) ) {

	// 		$rc = new ReflectionClass($this->controller);

	// 		if( $rc->hasMethod($this->action) ) {

	// 			$method = $rc->getMethod($this->action);
	// 			$controller = $rc->newInstance();
				
	// 			$method->invoke($controller, $this->params);
			
	// 		}else{ Lib::error('404','404'); }
	// 	}else{ Lib::error('404', '404'); }
	
	// }

	// function route(){

	// 	if ( class_exists($this->controller) ){

	// 		$rc = new ReflectionClass($this->controller);

	// 		if ( !$rc->hasMethod($this->action) ){

	// 			$this->action = 'defaultAction';
	// 			$this->key = 1;
			
	// 		}

	// 		$method = $rc->getMethod( $this->action );
	// 		$controller = $rc->newInstance();

	// 		if ( !empty($this->path[$this->key]) ){
	// 			$this->params = array_slice($this->path, $this->key);
	// 		}
			
	// 		$method->invoke($controller, $this->params);

	// 	} else { Lib::error('404','404'); }
	
	// }

	function route(){

		if ( !class_exists($this->controller) ){
			$this->controller = 'ArticleController';
			$this->action = 'defaultAction';
		}

		$rc = new ReflectionClass($this->controller);

		if ( !$rc->hasMethod($this->action) ){
			$this->action = 'defaultAction';
			$key = 1;
		}else{
			$key = $this->controller == 'ArticleController' ? 0 : 2;
		}

		if ( isset($this->path[$key]) ){
			$this->params = array_slice($this->path, $key);
		}

		$method = $rc->getMethod($this->action);
		$controller = $rc->newInstance();
		$method->invoke($controller, $this->params);

	}

}