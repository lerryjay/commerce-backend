<?php 
  // namespace GREY\GCore;
  class GCDbconn
  {
		public function connect(){
			global $db_server;
			global $db_user;
			global $db_password;
			global $db; 
			try {
				$connection =  new mysqli($db_server, $db_user, $db_password, $db);
				return $connection;
			} catch (Exception $e) {
				return $e;
			}
			
		}
  }
?>