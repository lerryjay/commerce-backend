<?php
class GMUser extends GModel
{
    private $mainusertbl = 'users';

    /**
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function addUser(
        $email,
        $telephone,
        $username,
        $password,
        $tradeId,
        $role = 'user'
    ) {
        $insertUser = $this->db->insert('users', [
            'trade_id' => $tradeId,
            'email' => $email,
            'telephone' => $telephone,
            'username' => $username,
        ]);
        $this->db->insert('auth', [
            'user_id' => $insertUser,
            'password' => $password,
            'role' => $role,
        ]);
        return $insertUser;
    }

    public function update($userId, $data)
    {
        return $this->db->update($this->mainusertbl, $data, ['id' => $userId]);
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
    private function getusers()
    {
        return $this->db
            ->query($this->mainusertbl, [
                'users.id AS userid',
                'trade_id',
                'password',
                'username',
                'telephone',
                'email',
                'users.status',
                'role',
                'activation',
                'permissions',
                'tokenexpdate',
                'tokenexptime',
                'token',
            ])
            ->join('inner', [
                ['table' => 'auth', 'field' => 'user_id'],
                ['table' => 'users', 'field' => 'id'],
            ]);
    }

    public function getUserById($userId, $status = 1)
    {
        return $this->getusers()
            ->where_equal('users.status', $status)
            ->and_where('id', $userId)
            ->exec()->row;
    }
    public function getUserbyTradeId($tradeId, $status = 1)
    {
        return $this->getusers()
            ->where_equal('users.status', $status)
            ->and_where('trade_id', $tradeId)
            ->exec()->row;
    }

    public function getUserByLoginId($loginId, $status = 1)
    {
        return $this->getusers()
            ->where_equal('users.status', $status)
            ->and_where('email', $loginId, 'users')
            ->or_where('username', $loginId, 'users')
            ->or_where('telephone', $loginId, 'users')
            ->exec()->row;
    }

    public function getUserByHField($token, $status = 1)
    {
        return $this->getusers()
            ->where_equal('users.status', $status)
            ->and_where('hfield', $token, 'auth')
            ->exec()->row;
    }

    public function updateAuthToken(
        $userId,
        $token,
        $tokenexpdate,
        $tokenexptime,
        $stringtoken = 'OPEN'
    ) {
        return $this->db->update(
            'auth',
            [
                'token' => $token,
                'tokenexpdate' => $tokenexpdate,
                'tokenexptime' => $tokenexptime,
                'hfield' => $stringtoken,
            ],
            ['user_id' => $userId]
        );
    }

    public function updatePassword($userId, $password)
    {
        return $this->db->update(
            'auth',
            ['password' => $password, 'token' => rand(100000, 999999)],
            ['user_id' => $userId]
        );
    }

    public function updateStatus($userId, $status = 0)
    {
        return $this->update($userId, ['status' => 0]);
    }
    public function updateProfile(
        $userId,
        $telephone,
        $username,
        $firstname,
        $lastname,
        $othername,
        $countryid,
        $regionid
    ) {
        return $this->update($userId, [
            'telephone' => $telephone,
            'username' => $username,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'othername' => $othername,
            'country_id' => $countryid,
            'city_id' => $regionid,
        ]);
    }

    public function updateActivation($userId, $activation)
    {
        return $this->db->update(
            'auth',
            ['activation' => $activation],
            ['user_id' => $userId]
        );
    }

    public function addFilter($filter = [])
    {
        if (isset($filter['customerid']) && strlen($filter['customerid']) > 0) {
            $this->db->and_where(
                'id',
                $filter['customerid'],
                $this->mainusertbl
            );
        }
        if (isset($filter['activation']) && strlen($filter['activation']) > 0) {
            $this->db->and_where('activation', $filter['activation'], 'auth');
        }
        if (isset($filter['search']) && strlen($filter['search']) > 0) {
            $this->db->add_condition(
                'AND ( firstname LIKE % ? % OR lastname LIKE % ? % OR othernames LIKE % ? % or username LIKE % ? % OR email LIKE % ? %',
                [
                    $filter['search'],
                    $filter['search'],
                    $filter['search'],
                    $filter['search'],
                    $filter['search'],
                ]
            );
        }
        if (isset($filter['on']) && strlen($filter['on']) > 0) {
            $this->db->and_where(
                'createdat',
                $filter['on'],
                $this->mainusertbl
            );
        }
        if (isset($filter['role']) && strlen($filter['role']) > 0) {
            $this->db->and_where('role', $filter['role'], 'auth');
        }

        if (isset($filter['startdate']) && strlen($filter['startdate']) > 0) {
            $this->db->and_where_less_equal(
                'createdat',
                $filter['startdate'],
                $this->mainusertbl
            );
        }
        if (isset($filter['enddate']) && strlen($filter['enddate']) > 0) {
            $this->db->and_where_greater_equal(
                'createdat',
                $filter['enddate'],
                $this->mainusertbl
            );
        }

        if (isset($filter['limit'])) {
            $limit = $filter['limit'];
            $this->db->limit($filter['limit']);
        } else {
            $limit = 20;
            $this->db->limit(20);
        }

        if (isset($filter['pageno']) && strlen($filter['pageno'] > 0)) {
            $this->db->offset(($filter['pageno'] - 1) * $limit);
        } else {
            $this->db->offset(0);
        }

        if (isset($filter['sort'])) {
            $sort = $filter['sort'];
        } else {
            $sort = 'createdat';
        }

        if (!isset($filter['order']) && strlen($filter['order'] > 0)) {
            $order = $filter['order'];
        } else {
            $order = 'DESC';
        }

        if ($order == 'DESC') {
            $this->db->order_by_desc($this->mainusertbl, $sort);
        } else {
            $this->db->order_by_asc($this->mainusertbl, $sort);
        }

        return $this->db;
    }

    public function getCustomers($filter = [], $status = 1)
    {
        $filter['role'] = 'user';
        $this->getusers()->where_equal('users.status', $status);
        return $this->addFilter($filter)->exec()->rows;
    }
}
