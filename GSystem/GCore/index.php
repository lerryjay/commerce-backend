<?php 
  echo 'BASE_PATH'.BASE_PATH."++++++++++++++++++++++++++++++++++";
  require_once BASE_PATH.'/GSystem/GCore/gc_loader.php';
  require_once BASE_PATH.'/GSystem/GCore/gc_handler.php';
	require_once BASE_PATH.'/GSystem/GCore/gc_router.php';
	require_once BASE_PATH.'/GSystem/GCore/gc_core.php';
  require_once BASE_PATH.'/GSystem/GConfig/index.php';
  try {
    if(file_exists( BASE_PATH.'/vendor/autoload.php')){
      require_once BASE_PATH.'/vendor/autoload.php';
    }
  } catch (\Throwable $th) {
  }
  // use GREY\GCore\GCLoader;
  // use GREY\GCore\GCRouter;
  // use GREY\GCore\GCore;

  // spl_autoload_register(function ($class_name) {
  //     include $class_name . '.php';
  // });

  if (!PRODUCTION_MODE){
    ini_set("display_errors","On");
    error_reporting(E_ALL);
  }else{
    ini_set("display_errors","OFF");
  }

  $load = new GCLoader();
  (LOAD_DATABASE != null  && LOAD_DATABASE ) ?  $load->db() : null;

  try{
    $load->preloadlibrary();
    $router = new GCRouter();	
    $routes = $router->getroute();

    if( $routes[0] == 'public') $load->public(implode("/",$routes));
    else $load->route($routes[0],$routes[1]);
  }catch(\Throwable  $th){
    new GCHandler($th);
    http_response_code ($code);
    exit(json_encode(['status'=>false,'message'=>'Internal Server error']));
  }
?>
