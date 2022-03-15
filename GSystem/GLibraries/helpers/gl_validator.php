<?php 
  class GLHelpersValidator  extends GLibrary{
		public function  string($string,$minLength= 2,$maxLength = 20,$trim = true,$numbers = true,$characters = false,$html = false,$extraChars = [])
		{
			$string = trim($trim);
			$regexp = "/^[a-zA-Z 0-9!@#$,.-]*$/";

			return true;
		}
	  public function cleanString($string,$minLength = 1,$maxLength = 10)
	  {
			if (preg_match("/^[a-zA-Z ]*$/",$string) && strlen($string) >= $minLength && strlen($string) <= $maxLength ) return true;
			else return false;
		}
		

		public function dirtyString($string,$minLength = 1,$maxLength = 10)
		{
			if (preg_match("/^[a-zA-Z 0-9!@#$,.-]*$/",$string) && strlen($string) >= $minLength && strlen($string) <= $maxLength ) return true;
			else return false;
		}

		public function username($string,$minLength = 1,$maxLength = 30)
		{
			if (preg_match("/^[a-zA-Z 0-9_]*$/",$string) && strlen($string) >= $minLength && strlen($string) <= $maxLength ) return true;
			else return false;
		}

		public function int($int,$min = 1,$max = 10000000)
		{
			if (preg_match("/^[0-9]*$/",$int) && $int >= $min && $int <= $max) return true;
			else return false;
		}

		public function double($input)
		{
			return preg_match('/^[0-9]+(?:\.[0-9]{0,2})?$/', $input) ? true : false;
		}

		public function email($email)
		{
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
			else return true;
		}

		public function telephone($telephone)
		{ 
			if(!filter_var($telephone, FILTER_SANITIZE_NUMBER_INT)) return false;
			else return true;
    }
    
    public function  dateFormat1($test_date) //date format DD/MM/YYYY
    { 
      $test_date = date('d-m-Y',strtotime($test_date));
      $test_arr  = explode('-', $test_date);
      return count($test_arr) == 3 ?  checkdate($test_arr[1], $test_arr[0], $test_arr[2]) : false; 
    }

    public function validateUrl($url)
    {
      // Remove all illegal characters from a url
      $url = filter_var($url, FILTER_SANITIZE_URL);
      // Validate url
      if (filter_var($url, FILTER_VALIDATE_URL)){ return true ;}
      else { return false; }
		}
		
		public function password($password)
		{
			if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}$/', $password)) {
				return false;
			}else return true;
		}

    // public function validateDateFormat2

	
			
			
		}
?>