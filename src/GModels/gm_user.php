<?php 
  class GMUser Extends GModel{
    private $mtablename = 'users';
    private function getusers()
    {
      return $this->db->query( $this->mtablename, ['username','telephone','email','trade_id  AS tradeid, id AS userid']);
    }

    private function getlogin()
    {
      $this->db->query('login',array("login.*,username,email,telephone,trade_id, users.id AS userid"));
      $this->db->join('inner',
        [
          [
            "table"=>"users",
            "field"=>"id"
          ],
          [
            "table"=>"login",
            "field"=>"user_id"
          ]
        ]
      );   
      return;
    }


  	public function addUser($email,$telephone,$username,$tradeId)
  	{
      return $this->db->insert($this->mtablename,
        array(
          'username'=>$username,
          'email'=>$email,
          'telephone'=>$telephone,
          'trade_id'=>$tradeId
        )
      );
  	}

    public function addLogin($userId,$password,$role = 1)
    {
      return $this->db->insert('login',
        array(
          'user_id' => $userId,
          'password'=> $password,
          'role'=>$role
        )
      );
    }
    public function getUserByLoginId($loginId)
    {
      $this->getlogin();
      $this->db->where_equal("email",$loginId,'','users');
      $this->db->or_where("username",$loginId,'users');
      $this->db->or_where("telephone",$loginId,'users');
      return $this->db->exec()->row;
    }

    public function getLoginByUsernameOrEmail($loginId)
    {
      $this->getlogin();
      $this->db->where_equal("email",$loginId,'','users');
      $this->db->or_where("username",$loginId,'users');
      return $this->db->exec()->row;
    }

    public function getLoginByUsernameOrTel($loginId)
    {
      $this->getlogin();
      $this->db->where_equal("telephone",$loginId,'','users');
      $this->db->or_where("username",$loginId,'users');
      return $this->db->exec()->row;
    }

    public function getLoginbyUserId($userId,$status = 1)
    {
      $this->getlogin();
      $this->db->where_equal("user_id",$userId,'','login');
      $this->db->and_where('status',$status);
      return $this->db->exec()->row;
    }

    public function getLoginbyTradeId($tradeId,$status = 1)
    {
      $this->getlogin();
      $this->db->where_equal("trade_id",$tradeId,'','users');
      $this->db->and_where('status',$status);
      return $this->db->exec()->row;
    }

    public function updatePassword($userId,$newPassword)
    {
      return $this->db->update('login',
        [
          'password'=>$this->encrypt->password($newPassword)
        ],
        [
          'user_id'=>$userId
        ]
      );
    }

    public function updateToken($userId,$token,$tokenexpdate)
    {
      return $this->db->update('login',
        [
          'token'=>$token,
          'token_expiry_date'=>$tokenexpdate

        ],
        [
          'user_id'=>$userId
        ]
      );
    }

    public function getUserbyEmail($email,$status = 1)
    {
      $this->getusers();
      $this->db->where_equal('email',$email);
      $this->db->and_where('status',$status);
      return $this->db->execute();
    }

    public function getUserbyTelephone($telephone,$status = 1)
    {
      $this->getusers();
      $this->db->where_equal('telephone',$telephone);
      $this->db->and_where('status',$status);
      // $this->db->echoSql();
      return $this->db->execute();
    }

    public function getUserbyUsername($username,$status = 1)
    {
      $this->getusers();
      $this->db->where_equal('username',$username);
      $this->db->and_where('status',$status);
      return $this->db->execute();
    }

    public function getUserbyTradeId($tradeId,$status = 1)
    {
      $this->getusers();
      $this->db->where_equal('trade_id',$tradeId);
      $this->db->and_where('status',$status);
      return $this->db->exec()->row;
    }


  }
?>