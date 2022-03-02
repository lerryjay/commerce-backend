<?php

  $default = 'global';
  $path = explode('/', trim($_SERVER['PHP_SELF'],'/'));
 	
//   define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'].'/'.$path[0]);
  define('BASE_PATH', '/app'.'/');
  require_once 'GSystem/GCore/index.php';
?>
