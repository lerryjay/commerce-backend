<?php
class GCUser extends GController
{
    public function index()
    {
        $jwtdata = $this->request->validate_jwt_token();
        $this->userId = $jwtdata['bearer'];
        isset($jwtdata['isadmin']) ? $this->list() : $this->profile();
    }

    public function add()
    {
        $user = $this->load->helper('auth/user', 'adminHasPermission', [
            'CREATECUSTOMER',
        ]);
        $password = $this->encrypt->generatePassword();
        $data = $this->load->helper(
            'validator/user',
            'signup',
            array_merge(
                ['password' => $password],
                $this->request->post([
                    'firstname',
                    'lastname',
                    'othername',
                    'email',
                    'telephone',
                ])
            )
        );
        extract($data);
        $insert = $this->model_user->addUser(
            $email,
            $telephone,
            $username,
            $password,
            $firstname,
            $lastname,
            $othername
        );
        if ($insert) {
            // send account creation mail
            $this->request->emit([
                'status' => true,
                'message' => 'Registration Successful',
            ]);
        }
        $this->request->emit([
            'status' => false,
            'message' => 'Registration failed',
        ]);
    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function list()
    {
        $user = $this->load->helper('auth/user', 'adminHasPermission', [
            'LISTCUSTOMERS',
        ]);
        $this->load->model('user');

        $filter = $this->request->get([
            'startdate',
            'enddate',
            'on',
            'pageno',
            'limit',
            'order',
            'sort',
            'customerid',
            'activation',
            'search',
        ]);
        $list = $this->model_user->getCustomers($filter);
        if ($list) {
            foreach ($list as $customer) {
                $customers[] = [
                    'userid' => $customer['userid'],
                    'email' => $customer['email'],
                    'firstname' => $customer['firstname'],
                    'lastname' => $customer['lastname'],
                    'othername' => $customer['othername'],
                    'telephone' => $customer['telephone'],
                    'region' => $customer['region'],
                    'country' => $customer['country'],
                    'activation' => $customer['activation'],
                ];
            }
            $total = $this->model_user->getTotalCustomers($filter);
            $pageno = $filter['pageno'] ?? 1;
            $this->request->emit([
                'status' => true,
                'message' => 'Customers retrived successfully',
                'data' => [
                    'customers' => $customers,
                    'total' => count($total),
                    'pageno' => $pageno,
                ],
            ]);
        }
        $this->request->emit([
            'status' => true,
            'message' => 'No customers matching the filter',
            'data' => ['customers' => [], 'total' => 0, 'pageno' => 1],
        ]);
    }

    public function profile()
    {
        $user = $this->load->helper('auth/user', 'isUser');
        $user = [
            'email' => $user['email'],
            'username' => $user['username'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname'],
            'othername' => $user['othername'],
            'telephone' => $user['telephone'],
            'countryid' => $user['countryid'],
            'regionid' => $user['regionid'],
            'activation' => $user['activation'],
        ];
        $this->request->emit([
            'status' => true,
            'message' => 'User profile retrieved successfully',
            'data' => $user,
        ]);
    }

    public function userprofile()
    {
        $user = $this->load->helper('auth/user', 'adminHasPermission', [
            'VIEWCUSTOMER',
        ]);

        $userid = $this->request->get('userid');
        $this->load->model('user');
        $user = $this->model_user->getUserById($userid);

        $user = [
            'email' => $user['email'],
            'username' => $user['username'],
            'firstname' => $user['firstname'],
            'lastname' => $user['lastname'],
            'othername' => $user['othername'],
            'telephone' => $user['telephone'],
            'countryid' => $user['countryid'],
            'regionid' => $user['regionid'],
            'activation' => $user['activation'],
        ];
        $this->request->emit([
            'status' => true,
            'message' => 'User profile retrieved successfully',
            'data' => $user,
        ]);
    }

    public function register()
    {
        $data = $this->load->helper(
            'validators/user',
            'signup',
            $this->request->post([
                'firstname',
                'lastname',
                'othername',
                'password',
                'email',
                'telephone',
            ])
        );
        extract($data);

        $this->load->model('user');
        $userExists = $this->model_user->getUserByLoginId($email);
        if ($userExists) {
            $this->request->emit([
                'status' => false,
                'message' => 'Email is associated with another account',
                'field' => 'email',
            ]);
        }
        $userExists = $this->model_user->getUserByLoginId($telephone);
        if ($userExists) {
            $this->request->emit([
                'status' => false,
                'message' =>
                    'Telephone number is associated with another account',
                'field' => 'telephone',
            ]);
        }
        $username = $username ?? '';
        $insert = $this->model_user->addUser(
            $email,
            $telephone,
            $username,
            $this->encrypt->password($password),
            $firstname,
            $lastname,
            $othername
        );
        if ($insert) {
            // send account creation mail
            $this->request->emit([
                'status' => true,
                'message' => 'Registration Successful',
            ]);
        }
        $this->request->emit([
            'status' => false,
            'message' => 'Registration failed',
        ]);
    }

    public function login()
    {
        $data = $this->load->helper(
            'validators/user',
            'login',
            $this->request->post(['password', 'loginid'])
        );
        extract($data);

        $this->load->model('user');
        $userExists = $this->model_user->getUserByLoginId($loginId);
        if ($userExists) {
            extract($userExists);
            if (password_verify($this->request->post('password'), $password)) {
                // send login mail
                $token = $this->encrypt->generate_jwt(
                    [
                        'bearer' => $userExists['userid'],
                        'logindate' => date('Y-m-d H:i:s'),
                    ],
                    10
                );
                $refreshtoken = $this->encrypt->generate_jwt(
                    [
                        'nature' => 'refresh',
                        'bearer' => $userExists['userid'],
                        'logindate' => date('Y-m-d H:i:s'),
                    ],
                    30
                );
                $userData = [
                    'email' => $userExists['email'],
                    'username' => $userExists['username'],
                    'firstname' => $userExists['firstname'],
                    'lastname' => $userExists['lastname'],
                    'othername' => $userExists['othername'],
                    'telephone' => $userExists['telephone'],
                    'token' => $token,
                    'expires' => date('Y-m-d H:i:s', strtotime('10 minutes')),
                    'refreshtoken' => $refreshtoken,
                ];
                $this->request->emit([
                    'status' => true,
                    'message' => 'Login successful',
                    'data' => $userData,
                ]);
            }
        }
        $this->request->emit([
            'status' => false,
            'message' => 'Invalid username or password',
        ]);
    }

    public function forgotpassword()
    {
        $loginid = $this->request->post('loginid');
        $this->load->model('user');
        $userExists = $this->model_user->getUserByLoginId($loginid);
        if (!$userExists) {
            $this->request->emit([
                'status' => false,
                'message' =>
                    'Account cannot be verified or user does not exist',
            ]);
        }
        $token = rand(100000, 999999);
        $tokenexpdate = date('Y-m-d', strtotime('+ 30 minutes'));
        $tokenexptime = date('H:i:s', strtotime('+ 30 minutes'));
        $updated = $this->model_user->updateAuthToken(
            $userExists['userid'],
            $token,
            $tokenexpdate,
            $tokenexptime
        );

        $string = $this->encrypt->generate_jwt([
            'bearer' => $userExists['userid'],
            'token' => $token,
        ]);

        //send forgot password mail here
        $this->request->emit([
            'status' => true,
            'message' => 'A reset link has been sent to the registered email',
            'token' => $string,
        ]);
    }

    public function resendtoken()
    {
        $loginid = $this->request->post('loginid');
        $this->load->model('user');
        $userExists = $this->model_user->getUserByLoginId($loginid);
        if (!$userExists) {
            $this->request->emit([
                'status' => false,
                'message' =>
                    'Account cannot be verified or user does not exist',
            ]);
        }
        $token = rand(100000, 999999);
        $tokenexpdate = date('Y-m-d', strtotime('+ 30 minutes'));
        $tokenexptime = date('H:i:s', strtotime('+ 30 minutes'));
        $updated = $this->model_user->updateAuthToken(
            $userExists['userid'],
            $token,
            $tokenexpdate,
            $tokenexptime
        );

        $string = $this->encrypt->generate_jwt([
            'bearer' => $userExists['userid'],
            'token' => $token,
        ]);

        //send forgot password mail here
        $this->request->emit([
            'status' => true,
            'message' => 'A reset link has been sent to the registered email',
            'token' => $string,
        ]);
    }

    public function verifytoken()
    {
        extract($this->request->post(['token', 'loginid']));
        if (!$this->validator->string($token) || strlen($token) < 6) {
            $this->request->emit([
                'status' => false,
                'message' => 'User cannot be verifed',
            ]);
        }
        if (strlen($token) > 6) {
            $tokenValid = $this->encrypt->verify_jwt($token);
            $this->request->emit($tokenValid);
        } else {
            $this->load->model('user');
            $userExists = $this->model_user->getUserByLoginId($loginid);
            if ($userExists && $userExists['token'] == $token) {
                $data = [
                    'bearer' => $userExists['userid'],
                    'token' => $userExists['token'],
                    'exp' =>
                        $userExists['tokenexpdate'] .
                        ' ' .
                        $userExists['tokenexptime'],
                ];
                $this->request->emit([
                    'status' => true,
                    'message' => 'Valid token',
                    'data' => $data,
                ]);
            }
        }
        $this->request->emit([
            'status' => false,
            'message' => 'Invalid authentication token',
            'code' => 401,
        ]);
    }

    public function resetpassword()
    {
        extract($this->request->post(['password', 'token', 'bearer']));
        if (!$this->validator->int($token, 100000, 999999)) {
            $this->request->emit([
                'status' => false,
                'message' => 'Invalid Parameter',
                'field' => 'token',
            ]);
        }
        if (!$this->validator->password($password)) {
            $this->request->emit([
                'status' => false,
                'message' => 'Invalid Parameter',
                'field' => 'password',
            ]);
        }
        $this->load->model('user');
        $userExists = $this->model_user->getUserById($bearer);
        if (
            $userExists &&
            $userExists['token'] == $token &&
            strtotime(date('Y-m-d H:i:s')) <=
                strtotime(
                    $userExists['tokenexpdate'] .
                        ' ' .
                        $userExists['tokenexptime']
                )
        ) {
            $password = $this->encrypt->password(
                $this->request->post('password')
            );
            $this->model_user->updatePassword($userExists['userid'], $password);
            $this->request->emit([
                'status' => true,
                'message' => 'Password updated successfully',
            ]);
        } else {
            $this->request->emit([
                'status' => false,
                'message' => 'Token cannot be verified or invalid token!',
            ]);
        }

        $this->request->emit([
            'status' => false,
            'message' => 'Password update failed. User cannot be verified!',
        ]);
    }

    public function changepassword()
    {
        $user = $this->request->validate_jwt_token();
        $this->userId = $user['bearer'];
        extract($this->request->post(['oldpassword', 'password']));

        if (!$this->validator->password($oldpassword)) {
            $this->request->emit([
                'status' => false,
                'message' => 'Invalid Parameter',
                'field' => 'oldpassword',
            ]);
        }
        $this->load->model('user');
        $userExists = $this->model_user->getUserById($this->userId);
        if ($userExists) {
            if (password_verify($oldpassword, $userExists['password'])) {
                $this->model_user->updatePassword(
                    $this->userId,
                    $this->encrypt->password($this->request->post('password'))
                );
                $this->request->emit([
                    'status' => true,
                    'message' => 'Password updated successfully',
                ]);
            }
        }
        $this->request->emit([
            'status' => false,
            'message' =>
                'User cannot be verified and request could not be completed',
            'code' => 401,
        ]);
    }

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function address()
    {
        $user = $this->load->helper('auth/user', 'isUser');
        $this->userId = $user['userid'];
        $filter = [
            'startdate',
            'enddate',
            'on',
            'pageno',
            'limit',
            'order',
            'sort',
            'userid',
            'cityid',
            'countryid',
        ];
        extract($this->request->get($filter));

        $this->load->model('address');
        $addresses =
            $user['role'] == 'admin'
                ? $this->model_address->getAddress($filter)
                : $this->model_address->getUserAddresses($this->userId);
        $addresses = $addresses ?: [];
        $this->request->emit([
            'status' => true,
            'message' => 'Addresses retrieved succssfully',
            'data' => $addresses,
        ]);
    }

    /**
     * undocumented function summary
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function updateprofile()
    {
        $user = $this->load->helper('auth/user', 'isUser');
        $this->userId = $user['userid'];
        $data = $this->load->helper(
            'validator/user',
            'profile',
            $this->request->post([
                'telephone',
                'username',
                'firstname',
                'lastname',
                'othername',
                'countryid',
                'regionid',
                'postalcode',
            ])
        );
        extract($data);

        $this->load->model('user');
        $userExists = $this->model_user->getUserByLoginId($username);
        if ($userExists && $userExists['userid'] !== $this->userId) {
            $this->request->emit([
                'status' => false,
                'message' => 'Username is associated with another account',
                'data' => ['field' => 'username'],
                'code' => 400,
            ]);
        }
        $userExists = $this->model_user->getUserByLoginId($telephone);
        if ($userExists && $userExists['userid'] !== $this->userId) {
            $this->request->emit([
                'status' => false,
                'message' => 'Telephone associated with another account',
                'data' => ['field' => 'telephone'],
                'code' => 400,
            ]);
        }

        $update = $this->model_user->updateProfile(
            $this->userId,
            $telephone,
            $username,
            $firstname,
            $lastname,
            $othername,
            $countryid,
            $regionid
        );
        if ($update) {
            $this->request->emit([
                'status' => true,
                'message' => 'Update successful',
            ]);
        } else {
            $this->request->emit([
                'status' => false,
                'message' => 'Update failed',
            ]);
        }
    }

    /**
     * undocumented function summary
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function activate()
    {
        $this->load->model('user');
        $token = $this->request->get('token');
        $user = $this->model_user->getUserByHField($token);
        if (!$user) {
            $this->request->emit([
                'status' => false,
                'message' => 'Unable to activate account! User not found!',
                'code' => 404,
            ]);
        }
        $update = $this->model_user->updateActivation(
            $user['userid'],
            'activated'
        );
        if (!$update) {
            $this->request->emit([
                'status' => true,
                'message' => 'An uexpected error!',
                'code' => 500,
            ]);
        }
        $this->request->emit([
            'status' => true,
            'message' => 'Account activated successfully',
        ]);
    }

    /**
     * undocumented function summary
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function activateuser()
    {
        $this->load->controller('admin');
        $admin = $this->controller_admin->checkRequestPermission(
            'EDITCUSTOMER'
        );
        $this->load->model('user');

        $customerId = $this->request->get('userid');
        $update = $this->model_user->updateActivation($customerId, 'activated');
        if (!$update) {
            $this->request->emit([
                'status' => true,
                'message' => 'An uexpected error!',
                'code' => 500,
            ]);
        }
        $this->request->emit([
            'status' => true,
            'message' => 'Account activated successfully',
        ]);
    }

    /**
     * undocumented function summary
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function suspend()
    {
        $this->load->controller('admin');
        $admin = $this->controller_admin->checkRequestPermission(
            'EDITCUSTOMER'
        );
        $this->load->model('user');

        $customerId = $this->request->get('userid');
        $update = $this->model_user->updateActivation($customerId, 'suspended');
        if (!$update) {
            $this->request->emit([
                'status' => true,
                'message' => 'An uexpected error!',
                'code' => 500,
            ]);
        }
        $this->request->emit([
            'status' => true,
            'message' => 'Account suspend successfully',
        ]);
    }

    /**
     * undocumented function summary
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function delete()
    {
        $this->load->controller('admin');
        $admin = $this->controller_admin->checkRequestPermission(
            'EDITCUSTOMER'
        );
        $this->load->model('user');

        $customerId = $this->request->get('userid');
        $update = $this->model_user->updateStatus($customerId, 0);
        if (!$update) {
            $this->request->emit([
                'status' => true,
                'message' => 'An uexpected error!',
                'code' => 500,
            ]);
        }
        $this->request->emit([
            'status' => true,
            'message' => 'Account deleted successfully',
        ]);
    }

    /**
     * undocumented function summary
     *
     * @param Type $var Description
     * @return type
     * @throws condition
     **/
    public function refreshtoken()
    {
        $data = $this->request->validate_jwt_token();
        $bearer = $data['bearer'];
        $nature = $data['nature'];
        $exdate = $data['exp'];

        $refresh = 10 * 60 * 1000;
        $res = [];
        $res['expires'] = date('Y-m-d H:i:s', strtotime('+ 10 minutes'));
        $res['token'] = $this->encrypt->generate_jwt($data, 10);
        if (strtotime($exdate) - strtotime('now') < $refresh) {
            $res['refreshtoken'] = $this->encrypt->generate_jwt($data, 30);
        }
        $this->request->emit([
            'status' => true,
            'message' => 'Login successful',
            'data' => $res,
        ]);
    }
} ?> 
