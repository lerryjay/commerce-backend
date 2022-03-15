<?php
class GCCliComposer extends CommandController
{
  public function run($argv)
  {
    array_splice($argv,0,2);
    echo shell_exec("composer ".implode(' ',$argv));
  }
}