<?php
class GMAdmin extends GModel
{
    private $mainadmintbl = 'admin';

    /**
     * undocumented function summary
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function add($userId, $role, $permissions)
    {
        $id = uniqid();
        return $this->db->insert($this->mainadmintbl, [
            'id' => $id,
            'user_id' => $userId,
            'role' => $role,
            'permissions' => $permissions,
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
    private function getadministrators($fields = [])
    {
        return $this->db
            ->query(
                $this->mainadmintbl,
                array_merge($fields, [
                    'admin.user_id AS userid',
                    'admin.permissions',
                    'admin.activation',
                    'users.firstname',
                    'users.lastname',
                    'users.email',
                    'users.telephone',
                    'users.username',
                ])
            )
            ->join('inner', [
                ['table' => 'users', 'field' => 'id'],
                ['table' => 'admin', 'field' => 'user_id'],
            ])
            ->join('inner', [
                ['table' => 'auth', 'field' => 'user_id'],
                ['table' => 'admin', 'field' => 'user_id'],
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
    public function getAdministratorByUserId($userId, $status = 1)
    {
        return $this->getadministrators()
            ->where_equal('admin.user_id', $userId)
            ->and_where('status', $status, 'admin')
            ->exec()->row;
    }

    public function addFilter($filter = [])
    {
        if (isset($filter['customerid']) && strlen($filter['customerid']) > 0) {
            $this->db->and_where(
                'id',
                $filter['customerid'],
                $this->mainadmintbl
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
                $this->mainadmintbl
            );
        }
        if (isset($filter['role']) && strlen($filter['role']) > 0) {
            $this->db->and_where('role', $filter['role'], 'auth');
        }

        if (isset($filter['startdate']) && strlen($filter['startdate']) > 0) {
            $this->db->and_where_less_equal(
                'createdat',
                $filter['startdate'],
                $this->mainadmintbl
            );
        }
        if (isset($filter['enddate']) && strlen($filter['enddate']) > 0) {
            $this->db->and_where_greater_equal(
                'createdat',
                $filter['enddate'],
                $this->mainadmintbl
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
            $this->db->order_by_desc($this->mainadmintbl, $sort);
        } else {
            $this->db->order_by_asc($this->mainadmintbl, $sort);
        }

        return $this->db;
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
    public function fetchAdministrators($filter = [], $status = 1)
    {
        $filter['role'] = 'admin';
        $this->getadministrators()->where_equal('admin.status', $status);
        return $this->addFilter($filter)->exec()->rows;
    }
} ?> 
