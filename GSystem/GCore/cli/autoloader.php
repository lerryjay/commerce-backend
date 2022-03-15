<?php
/**
 * Example project-specific auto-loading implementation.
 *
 * After registering this autoload function with SPL, the namespaces on the left of the loaders array will load classes found in the paths on the right.
 *
 * @param string $class The fully-qualified class name.
 * @return void
 */

$path = explode('/', trim($_SERVER['PHP_SELF'],'/'));
 	
define('__CLI_PATH__', $_SERVER['DOCUMENT_ROOT'].'/'.$path[0].'/GCore/cli/');

 spl_autoload_register(function ($class) {
   $file = __DIR__ .'/gc_'.strtolower( str_replace('GCCli', '',
   $class)).'.php';

    if (file_exists($file)) {
        require $file;
        return;
    }

});