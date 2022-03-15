<?php
class GMSeller extends GModel
{
    private $sellertablename = 'msellers';
    private function getsellers($fields = [])
    {
        $this->db->query(
            $this->sellertablename,
            array_merge(
                [
                    'msellers.id AS msellerid',
                    'msellers.storeid',
                    'msellers.name AS storename',
                    'msellers.slogan',
                    'msellers.rating',
                    'msellers.shortinfo',
                    'msellers.address',
                    'country.name AS country_name',
                    'country.id AS country_id',
                    'city.name AS city_name',
                    'city.state AS city_state',
                    'city.id AS city_id',
                    'users.trade_id AS tradeid',
                    'users.id AS userid',
                ],
                $fields
            )
        );

        $this->db->join('LEFT', [
            [
                'field' => 'id',
                'table' => 'city',
            ],
            [
                'field' => 'city_id',
                'table' => 'msellers',
            ],
        ]);
        $this->db->join('inner', [
            [
                'field' => 'id',
                'table' => 'users',
            ],
            [
                'field' => 'user_id',
                'table' => 'msellers',
            ],
        ]);
        $this->db->join('LEFT', [
            [
                'field' => 'id',
                'table' => 'country',
            ],
            [
                'field' => 'country_id',
                'table' => 'msellers',
            ],
        ]);
        return;
    }

    public function addseller($userId, $name, $storeid, $city, $country)
    {
        return $this->db->insert($this->sellertablename, [
            'name' => $name,
            'user_id' => $userId,
            'storeid' => $storeid,
            'city_id' => (int) $city,
            'country_id' => (int) $country,
            'registration_date' => date('Y-m-d H:i:s'),
        ]);
    }

    public function updateSeller(
        $msellerId,
        $name,
        $address,
        $city,
        $country,
        $shortinfo,
        $slogan
    ) {
        return $this->db->update(
            $this->sellertablename,
            [
                'name' => $name,
                'address' => $address,
                'city' => $city,
                'country' => $country,
                'shortinfo' => $shortinfo,
                'slogan' => $slogan,
            ],
            [
                'id' => $msellerId,
            ]
        );
    }

    public function updateSellerShortInfo($msellerId, $shortinfo)
    {
        return $this->db->update(
            $this->sellertablename,
            [
                'shortinfo' => $shortinfo,
            ],
            [
                'id' => $msellerId,
            ]
        );
    }

    public function getAllSellers($status = 1)
    {
        $this->db = $this->load->library('db');
        $this->sellertablename = 'msellers';
        $this->getsellers();
        $this->db->where_equal('msellers.status', 1);
        return $this->db->exec()->rows;
    }

    public function getSellersByStoreid($storeid, $status = 1)
    {
        $this->getsellers();
        $this->db->where_equal('msellers.status', $status);
        $this->db->and_where('storeid', $storeid, 'msellers');
        return $this->db->exec()->rows;
    }

    public function getSellersByName($name, $status = 1)
    {
        $this->getsellers();
        $this->db->where_equal('msellers.status', $status);
        $this->db->and_where_like('name', '%' . $name . '%', 'msellers');
        return $this->db->exec()->rows;
    }

    public function getSellersByCountry($countryId, $status = 1)
    {
        $this->getsellers();
        $this->db->where_equal('status', $status, '', 'msellers');
        $this->db->and_where('country_id', $countryId, 'msellers');
        return $this->db->exec()->rows;
    }
    public function getSellersByCity($cityId, $status = 1)
    {
        $this->getsellers();
        $this->db->where_equal('status', $status, '', 'msellers');
        $this->db->and_where('city_id', $cityId, 'msellers');
        return $this->db->exec()->rows;
    }

    public function getSellerByRegDate($regDate, $status = 1)
    {
        $this->getsellers();
        $this->db->where_equal('status', $status, '', 'msellers');
        $this->db->and_where('registration_date', $regDate, 'msellers');
        return $this->db->exec()->rows;
    }
    public function getSellerProfile($sellerId, $status = 1)
    {
        // '(
        //   SELECT COUNT(*) FROM orders INNER JOIN delivery ON orders.delivery_id = delivery.id WHERE delivery.delivery_status = ? AND orders.mseller_id = msellers.id
        // ) AS completed_orders',
        // '(
        //   SELECT COUNT(*) FROM orders INNER JOIN delivery ON orders.delivery_id = delivery.id WHERE delivery.delivery_status = ? AND orders.mseller_id = msellers.id
        // ) AS transit_orders',
        // '(
        //   SELECT COUNT(*) FROM orders INNER JOIN delivery ON orders.delivery_id = delivery.id WHERE delivery.delivery_status = ? AND orders.mseller_id = msellers.id
        // ) AS pending_orders',
        // '(
        //   SELECT COUNT(*) FROM products WHERE products.mseller_id =  msellers.id
        // ) AS total_products'
        $this->getsellers();
        $this->db->where_equal('status', $status, '', 'msellers');
        $this->db->and_where('id', $sellerId, 'msellers');
        return $this->db->exec()->rows;
    }
    public function getUserSellerProfile($userId, $status = 1)
    {
        $this->getsellers();
        $this->db->where_equal('status', $status, '', 'msellers');
        $this->db->and_where('user_id', $userId, 'msellers');
        return $this->db->exec()->row;
    }
    public function getSellerbyTradeId($tradeId, $status = 1)
    {
        $this->getsellers();
        $this->db->where_equal('status', $status, '', 'msellers');
        $this->db->and_where('trade_id', $tradeId, 'users');
        return $this->db->exec()->row;
    }
    public function getSellerbyStoreId($storeId, $status = 1)
    {
        $this->getsellers();
        $this->db->where_equal('status', $status, '', 'msellers');
        $this->db->and_where('storeid', $storeId, 'msellers');
        return $this->db->exec()->row;
    }
    public function addSellerCategory($msellerid, $categoryid)
    {
        return $this->db->insert('msellercategory', [
            'mseller_id' => $msellerid,
            'category_id' => $categoryid,
        ]);
    }
    public function updateSellerCategoryStatus(
        $msellerid,
        $categoryid,
        $status = 0
    ) {
        return $this->db->update(
            'msellercategory',
            [
                'status' => $status,
            ],
            [
                'mseller_id' => $msellerId,
                'category_id' => $categoryid,
            ]
        );
    }
    private function getmsellercategory()
    {
        $this->db->query('msellercategory', ['*']);
    }
    private function sortsellers($sort = [])
    {
        $limit = isset($sort['limit']) ? $sort['limit'] : 30;
        $pageno = isset($sort['pageno']) ? $sort['pageno'] : 1;
        $offset = ($pageno - 1) * $limit;
        $this->db->limit($limit);
        $this->db->offset($offset);
        isset($sort['order_by']) && isset($sort['order_desc'])
            ? $this->db->order_by_desc('msellers', $sort['order_by'])
            : null;
        isset($sort['order_by']) && isset($sort['order_asc'])
            ? $this->db->order_by_asc('msellers', $sort['order_by'])
            : null;
    }
    public function getSellerCategory($msellerid, $status = 1)
    {
        $this->getmsellercategory();
        $this->db->where_equal('status', $status);
        $this->db->and_where('mseller_id', $msellerid);
        return $this->db->exec()->rows;
    }
    public function getCategorySellers($categoryid, $status = 1, $sort = [])
    {
        $this->getsellers();
        $this->db->join('inner', [
            [
                'field' => 'id',
                'table' => $this->sellertablename,
            ],
            [
                'field' => 'mseller_id',
                'table' => 'msellercategory',
            ],
        ]);
        $this->db->where_equal('msellers.status', $status);
        $this->db->and_where('status', $status, 'msellercategory');
        $this->db->and_where('category_id', $categoryid, 'msellercategory');
        $this->db->sortsellers($sort);
        return $this->db->exec()->rows;
    }
}
?>
