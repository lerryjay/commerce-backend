<?php
  abstract class CommandController{
    protected $app;
  
    abstract public function run($argv);
  
    public function __construct(GCCliMain $app)
    {
        $this->app = $app;
    }
  
    protected function getApp()
    {
        return $this->app;
    }
  }
?>