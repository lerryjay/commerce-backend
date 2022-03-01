<?php
class GCUser extends GController
{
    public function add()
    {
        $this->load->model('users');
        $valid = $this->validateRegistration($username, $email, $telephone);
        if (!isset($valid['status'])) {
            return $valid;
        }
        $tradeId = $this->encrypt->gen_rand_alpha_num(15, 'MO');
        $insert = $this->model_user->addUser(
            $email,
            $telephone,
            $username,
            $tradeId
        );
        if ($insert) {
            $this->model_user->addLogin($insert, $password);
        }
        $this->load->model('trader');
        $this->model_trader->addTrader($insert, '-', '-', 0, 0, date('Y-m-d'));
        return [
            'status' => true,
            'message' => 'User Registration successful. Happy Trading!',
        ];
    }

    public function register()
    {
        $data = $this->load->helper(
            'validators/user',
            'signup',
            $this->request->post(
                [
                    'firstname',
                    'lastname',
                    'othername',
                    'username',
                    'password',
                    'email',
                    'telephone',
                ],
                true
            )
        );
        extract($data);

        $this->load->model('user');
        if ($this->model_user->getUserByLoginId($email)) {
            $userExists = 'email';
        } elseif ($this->model_user->getUserByLoginId($telephone)) {
            $userExists = 'telephone';
        } elseif ($this->model_user->getUserByLoginId($username)) {
            $userExists = 'username';
        } else {
            $userExists = false;
        }

        if ($userExists) {
            $this->request->emit([
                'status' => false,
                'message' => "$userExists is associated with another account",
                'data' => ['field' => $userExists],
                'code' => 403,
            ]);
        }

        $username = $username ?? '';
        $tradeId = $this->encrypt->gen_rand_alpha_num(15, 'NG');
        $insert = $this->model_user->addUser(
            $email,
            $telephone,
            $username,
            $tradeId
        );
        if ($insert) {
            $this->model_user->addLogin(
                $insert,
                $this->encrypt->password($password)
            );
        }
        $this->load->model('trader');
        $this->model_trader->addTrader($insert, '-', '-', 0, 0, date('Y-m-d'));

        $traderData = $this->model_trader->getTraderByTradeId($tradeId);
        $token = $this->encrypt->generate_jwt(
            [
                'bearer' => $tradeId,
                'logindate' => date('Y-m-d H:i:s'),
            ],
            10
        );
        $refreshtoken = $this->encrypt->generate_jwt(
            [
                'nature' => 'refresh',
                'bearer' => $tradeId,
                'logindate' => date('Y-m-d H:i:s'),
            ],
            30
        );

        // send account creation mail
        $this->request->emit([
            'status' => true,
            'message' => 'Registration Successful',
            'data' => [
                'user' => $traderData,
                'refreshtoken' => $token,
                'token' => $refreshtoken,
            ],
        ]);
    }

    public function login()
    {
        try {
            $this->load->model('user');
            $data = $this->load->helper('validators/user', 'login');
            extract($data);

            $userExists = $this->model_user->getUserByLoginId($loginId);

            if (!$userExists) {
                $this->request->emit([
                    'status' => false,
                    'message' => 'Invalid username or password',
                    'code' => 401,
                ]);
            }

            if (!$this->encrypt->verify($password, $userExists['password'])) {
                $this->request->emit([
                    'status' => false,
                    'message' => 'Invalid username or password',
                    'code' => 401,
                ]);
            }

            $this->load->model('trader');
            $traderData = $this->model_trader->getTraderByUserId(
                $userExists['userid']
            );
            $token = $this->encrypt->generate_jwt(
                [
                    'bearer' => $traderData['trade_id'],
                    'logindate' => date('Y-m-d H:i:s'),
                ],
                10
            );
            $refreshtoken = $this->encrypt->generate_jwt(
                [
                    'nature' => 'refresh',
                    'bearer' => $traderData['trade_id'],
                    'logindate' => date('Y-m-d H:i:s'),
                ],
                30
            );
            $this->request->emit([
                'status' => true,
                'message' => 'Login Successful!',
                'data' => [
                    'user' => $traderData,
                    'refreshtoken' => $token,
                    'token' => $refreshtoken,
                ],
            ]);
        } catch (\Throwable $th) {
            throw new Exception($th, 1);
        }
    }

    public function updatePassword($tradeId, $oldpassword, $newPassword)
    {
        $this->load->model('users');
        $userExists = $this->model_user->getUserbyTradeId($tradeId);
        if (!$userExists['status']) {
            return ['error' => true, 'message' => 'Invalid user!'];
        }
        $loginData = $this->model_user->getLoginbyUserId(
            $userExists['data']['userid']
        );

        if (
            !$this->encrypt->verify($password, $loginData['data']['password'])
        ) {
            return ['error' => true, 'message' => 'Invalid password!'];
        }
        return $this->model_user->updatePassword(
            $userExists['data']['userid'],
            $newPassword
        );
    }

    public function forgotPassword($loginId)
    {
        $this->load->model('users');
        $userExists = $this->model_user->getLoginByUsernameOrTel($loginId);
        if (!$userExists['status']) {
            $userExists = $this->model_user->getLoginByUsernameOrEmail(
                $loginId
            );
        }
        if (!$userExists['status']) {
            return ['error' => true, 'message' => 'Invalid username/email!'];
        }

        $token = rand(100000, 999999);
        $savetoken = $this->model_user->updateToken(
            $userExists['data']['userid'],
            $token,
            date(
                'Y-m-d H:i:s',
                strtotime(date('Y-m-d H:i:s') . ' + 15 minutes')
            )
        );
        $this->load->library('alert');
        $this->library_alert->sms(
            $userExists['data']['telephone'],
            'Please use ' . $token . ' as your one-time token'
        );
        //token email too
        return [
            'status' => true,
            'message' => 'A token has been sent to you via email and telephone',
        ];
    }

    public function resendToken($tradeId)
    {
        $this->load->model('users');
        $userExists = $this->model_user->getUserbyTradeId($tradeId);
        if (!$userExists['status']) {
            return ['error' => true, 'message' => 'Invalid user!'];
        }
        $token = rand(100000, 999999);
        $savetoken = $this->model_user->updateToken(
            $userExists['data']['userid'],
            $token,
            date(
                'Y-m-d H:i:s',
                strtotime(date('Y-m-d H:i:s') . ' + 15 minutes')
            )
        );
        $this->load->library('alert');
        $this->library_alert->sms(
            $userExists['data']['telephone'],
            'Please use ' . $token . ' as your one-time token'
        );
        //token email too
        return [
            'status' => true,
            'message' => 'A token has been sent to you via email and telephone',
        ];
    }

    public function verifyToken($tradeId, $token)
    {
        $this->load->model('users');
        $userExists = $this->model_user->getLoginbyTradeId($tradeId);
        if (!$userExists['status']) {
            return ['error' => true, 'message' => 'Invalid user!'];
        }

        if ($token != $userExists['data']['token']) {
            return ['error' => true, 'message' => 'Invalid token!'];
        }
        if (date('Y-m-d H:i:s') <= $userExists['data']['token_expiry_date']) {
            return ['error' => true, 'message' => 'Token already expired!'];
        }

        return ['status' => true, 'message' => 'Token verified'];
    }

    public function resetPassword($tradeId, $password, $token)
    {
        $this->load->model('users');
        $userExists = $this->model_user->getUserbyTradeId($tradeId);
        if (!$userExists['status']) {
            return ['error' => true, 'message' => 'Invalid user!'];
        }
        $loginData = $this->model_user->getLoginbyUserId(
            $userExists['data']['userid']
        );

        if ($token != $loginData['data']['token']) {
            return ['error' => true, 'message' => 'Invalid token!'];
        }
        if (date('Y-m-d H:i:s') <= $loginData['data']['token_expiry_date']) {
            return ['error' => true, 'message' => 'Token already expired!'];
        }

        return $this->model_user->updatePassword(
            $userExists['data']['userid'],
            $newPassword
        );
    }
}
?>
