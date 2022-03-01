<?php
  class GCHandler {
    public $errorFile;
     function __construct(\Throwable $exception) { //Exception
      $errorcode = $exception->getCode();
      $errormessage = $exception->getMessage();
      $trace = $exception->getTraceAsString();
      $content = "$errorcode:\t\t $errormessage\n $trace";
      if (!PRODUCTION_MODE){
          header("Content-Type: text/plain");
          echo $content;
          exit;
      }else{
        $this->writeErrorToFile($content);
      }
    }

    private function openErrorFile(){
      if(!file_exists(ERROR_LOG_FILE)) $this->createErrorFile();
      return  $this->errorFile = fopen( ERROR_LOG_FILE, "a" );
    }

    private function writeErrorToFile($message){
      $this->openErrorFile();
      fwrite($this->errorFile, date(DATE_RFC822)."\t".$message."\n" );
      $this->closeErrorFile();
    }

    private function createErrorFile()
    {
      $file = fopen(ERROR_LOG_FILE, "w") or die("Fatal error!");
      fwrite($file);
      fclose($file);
    }

    private function closeErrorFile()
    {
      fclose( $this->errorFile );
    }
  }

  class GError extends Exception 
  {
    function __construct(Throwable $exception, $customMessage = '',$customCode = '')
    {
        if(strlen($customMessage) > 0){ 
          $this->message = $customMessage;
        }else $this->message = $exception->getMessage();
        if(strlen($customCode) > 0){ $this->code = $customCode;}
        else $this->code = $exception->getCode();
    }
  }

  class GCDBError extends GError
  { 
    
  }

  class GCRouteError extends GError
  {}

  class GCHelperError extends GError
  {}

  class GCLoaderError extends GError
  {}
?>