<?php
  class GCCliTestController extends CommandController
  {
      
    public function run($argv)
    {
      $classname = $argv[3];
      if($argv[2] == 'controller'){
        $base = "GControllers/gc_";
        $txt = "<?php \n\tclass GC". ucfirst($argv[3])." extends GController{\n\n\t\tpublic function index(){ \n \t\t\t echo '$classname was created successfully'; \n \t \t} \n \t} \n ?> " ;
      }elseif($argv[2] == 'model'){
        $base = "GModels/gm_";
        $txt = "<?php \n \tclass GM". ucfirst($argv[3])." extends GModel{ \n\n \t\tprivate $"."main".$classname."tbl = ''; \n \t} \n ?> " ;
      }elseif ($argv[2] == 'route') {
        $base = "GRoutes/gr_";
        $txt = "<?php \n \tclass GR". ucfirst($argv[3])." extends GRoute{ \n\n \t\tpublic function index(){ \n \t\t\t echo '$classname was created successfully'; \n \t \t} \n \t} \n ?> " ;
      }

      $myfile = fopen('./src/'.$base.$classname.".php", "w") or die("Unable to open file!");
      
      fwrite($myfile, $txt);

      fclose($myfile);

      $title = ucfirst($argv[2]).' '.$argv[3];
      $this->getApp()->getPrinter()->display("$title  was created successfully");
    }
  }
?>