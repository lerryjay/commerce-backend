<?php
  class GHAuthPermission extends GHelpers{
    public function userHasPermission()
    {
      $this->load->model('user');
      $user  = $this->model_user->getUserById($this->userId);
      if(!$user)  $this->http->emit(['status'=>false,'message'=>'You do not have access to this resource','code'=>403]);
      $permissions = explode('|',$user['permissions']);
      if(!in_array($permission,$permissions))  $this->http->emit(['status'=>false,'message'=>'You do not have access to this resource','code'=>403]);
      else return $user;
    }

    public function adminHasPermission()
    {
      $requestData  = $this->http->validate_jwt_token();
      if(!$requestData['isadmin']) $this->http->emit(['status'=>false,'message'=>'You do not have access to this resource','code'=>403]);

      $this->userId = $requestData['bearer'];
      
      $this->load->model('admin');
      $admin  = $this->model_admin->getAdministratorByUserId($this->userId);
      if(!$admin)  $this->http->emit(['status'=>false,'message'=>'You do not have access to this resource','code'=>403]);
      $permissions = explode('|',$admin['permissions']);
      if(!in_array($permission,$permissions))  $this->http->emit(['status'=>false,'message'=>'You do not have access to this resource','code'=>403]);
      else return $admin;
    }

    public function isAdmin()
    {
      $requestData  = $this->http->validate_jwt_token();
      if(!$requestData['isadmin']) $this->http->emit(['status'=>false,'message'=>'You do not have access to this resource','code'=>403]);
      
      $this->userId = $requestData['bearer'];
      
      $this->load->model('admin');
      $admin  = $this->model_admin->getAdministratorByUserId($this->userId);
      if(!$admin)  $this->http->emit(['status'=>false,'message'=>'You do not have access to this resource','code'=>403]);
      else return $admin;
    }

    public function isUser()
    {
      $user = $this->request->validate_jwt_token();			
			$this->userId = $user['bearer'];
			$this->load->model('user');
      $user  = $this->model_user->getUserById($this->userId);
      return $user;
    }
  } 
?>