<?php 
  // namespace GREY\GCore;
  class GCore{
  	private $data;
	  function __construct()
		{
			global $load;
			global $dbconn;
			$this->dbconn = $dbconn;
			$this->load = clone $load;
			$this->load->setClass($this);
		}

		function __get($var){
			return isset( $this->data[$var]) ? $this->data[$var] : null;
		}

		function __set($var,$val){
			$this->data[$var] = $val;
		}
  }
  class GLibrary Extends GCore{}

  class GModel Extends GCore{}

  class GController Extends GCore{}

  class GRoute Extends GCore{}

  class GHelperS Extends GCore{}
?>