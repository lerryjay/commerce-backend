<?php 
  // namespace GREY\GCore;
  class GCRouter{
  	function __construct()
		{
			
		}

		function getroute(){
			global $default;
      $route = array(); 
		  if (isset($_SERVER['REQUEST_URI'] )){
		    $route = explode('/', trim(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),'/'));
		    array_shift($route);
		  }  
		  if (sizeof($route) == 0 || $route[0] == '') {
				$route[0] = $default;
				array_push($route,'index');
		  }else{
		  	if (!isset($route[1])) {
		      array_push($route,'index');
		    }
		  }
		  return $route;
		}

		function loadroute(){
			if (sizeof($route) == 0 || $route[0] == '') {
				require_once 'GRoutes/ga_'.$default.'.php';
		  	$loadApi = 'GR'.ucwords($default);
		  	$api = new $loadApi();
		    $api->index();
		  }else{
		  	require_once 'GRoutes/ga_'.$route[0].'.php';
		  	$loadApi = 'GR'.ucwords($route[0]);
		  	$api = new $loadApi();
		    if (isset($route[1])) {
		      $api->$route[1]();
		    }else{
		      $api->index();
		    }
			}
		}
  }
?>