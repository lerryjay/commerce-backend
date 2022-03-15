<?php 

// use Dotenv\Dotenv;
use Carbon\Carbon;
  class GLHelpersEncrypt extends GLibrary
  {
  	public function password($var){
			$options = [
			    'cost' => 10,
			];
			return password_hash($var, PASSWORD_BCRYPT, $options);
		}

		public function verify($var1,$var2){
			return password_verify($var1, $var2);
		}

		public function gen_rand_alpha_num($size = 20, $prefix = '') {
			$initialSize =  $size;
			$alpha_key = '';
	 		if (($size % 2) == 1) {	$size++; }
			$keys2 = range(0, 9);
			$alpha_key = md5(date(strtotime('now')));
			$size = $size - strlen($prefix);
			while (strlen($alpha_key) >= $size) { 
				$keys2 = range(0, 9);
				$alpha_key = substr_replace($alpha_key,'',rand(0,strlen($alpha_key)),1);
			}
			if ($prefix != '') {	$alpha_key = $prefix.'-'.$alpha_key;}
			if ($initialSize == $size) {
				return strtoupper($alpha_key);
			}else{
				return strtoupper($alpha_key);
			} 
    }
    
    public function generate_api_private_key()
    {
      return $secret = bin2hex(random_bytes(32));
    }

    public function base64UrlEncode($text)
    {
      return str_replace(
          ['+', '/', '='],
          ['-', '_', ''],
          base64_encode($text)
      );
    }

    public function generate_jwt($payload = [],$exp = 15)
    { 
      // $dotenv = new DotEnv(BASE_PATH);
      // $dotenv->load();
      $header = json_encode([
          'typ' => 'JWT',
          'alg' => 'HS256'
      ]);
      // get the local secret key
      // $secret = getenv('SECRET');
      // Encode Header
      $base64UrlHeader = $this->base64UrlEncode($header);
      // Encode Payload
      $payload['exp']  = Carbon::now()->addMinutes($exp);
      $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));
      // Create Signature Hash
      $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, JWT_SECRET_KEY, true);
      // Encode Signature to Base64Url String
      $base64UrlSignature = $this->base64UrlEncode($signature);
      // Create JWT
      $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
      return $jwt;
    } 

    public function verify_jwt($jwt)
    {
      // split the token
      $tokenParts = explode('.', $jwt);
      $header = base64_decode($tokenParts[0]);
      $payload = base64_decode($tokenParts[1]);
      $signatureProvided = $tokenParts[2];

      $base64UrlHeader = $this->base64UrlEncode($header);
      $base64UrlPayload = $this->base64UrlEncode($payload);
      $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload,JWT_SECRET_KEY, true);
      $base64UrlSignature = $this->base64UrlEncode($signature);

      // check the expiration time - note this will cause an error if there is no 'exp' claim in the token
      
      $expiration = Carbon::createFromTimestamp(strtotime(json_decode($payload)->exp));
      $tokenExpired = (Carbon::now()->diffInSeconds($expiration, false) < 0);


      // verify it matches the signature provided in the token
      $signatureValid = ($base64UrlSignature === $signatureProvided);

      if(!$signatureValid){
        return ['status'=>false, 'message'=>'Invalid authentication token', 'code'=>401];
      }elseif($tokenExpired){ return ['status'=>false, 'message'=>'Expired authentication token', 'code'=>401];}
      else{ return ['status'=>true, 'message'=>'Valid token', 'data'=>json_decode($payload,true)]; }
    }
  }
?>