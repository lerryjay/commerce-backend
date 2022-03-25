<?php
class GMProduct extends GModel
{
    private $maintbl = 'products';

    public function addProduct(
        $msellerid,
        $productname,
        $price,
        $quantity,
        $shortdescription,
        $visibility,
        $type
    ) {
        return $this->db->insert($this->maintbl, [
            'mseller_id' => $msellerid,
            'name' => $productname,
            'product_id' => uniqid(),
            'quantity' => $quantity,
            'price' => $price,
            'shortdescription' => $shortdescription,
            'visibility' => $visibility,
            'type' => $type,
        ]); //RECONSIDER PRODUCT HIDDEN<OPEN,DIGITAL,
    }

    public function addProductCategory($productId, $categoryId)
    {
        return $this->db->insert('productcategory', [
            'product_id' => $productId,
            'category_id' => $categoryId,
        ]);
    }

    public function addProductRelation($product1Id, $product2Id)
    {
        return $this->db->insert('productrelation', [
            'product1_id' => $product1Id,
            'product2_id' => $product2Id,
        ]);
    }
    public function addProductSalesRegion($productId, $regionId, $countryId)
    {
        return $this->db->insert('productsalesregion', [
            'product_id' => $productId,
            'region_id' => $regionId,
            'country_id' => $countryId,
        ]);
    }
    public function addProductMedia($productId, $mediaurl, $mediatype = 1)
    {
        return $this->db->insert('productmedia', [
            'product_id' => $productId,
            'mseller_id' => $mediatype,
            'mediaurl' => $mediaurl,
        ]);
    }
    public function addProductLocation($productId, $addressId)
    {
        return $this->db->insert('productlocation', [
            'product_id' => $productId,
            'address_id' => $addressId,
        ]);
    }

    public function update($productId, $data)
    {
        return $this->db->update($this->maintbl, $data, ['id' => $productId]);
    }

    public function updateProduct(
        $productId,
        $name,
        $sku,
        $price,
        $promo_price,
        $quantity,
        $shortdescription,
        $long_description,
        $manufacturer_id,
        $brand_id
    ) {
        return $this->db->update(
            $this->maintbl,
            [
                'name' => $name,
                'sku' => $sku,
                'promo_price' => $promo_price,
                'price' => $price,
                'quantity' => $quantity,
                'shortdescription' => $shortdescription,
                'long_description' => $long_description,
                'manufacturer_id' => $manufacturer_id,
                'brand_id' => $brand_id,
                'weight' => $weight,
            ],
            [
                'product_id' => $productId,
            ]
        );
    }
    public function updateProductViews($productId, $count = 1)
    {
        return $this->db->update(
            $this->maintbl,
            [
                'views' => 'views + ' . $count,
            ],
            [
                'id' => $productId,
            ]
        );
    }
    public function updateProductLikes($productId, $count = 1)
    {
        return $this->db->update(
            $this->maintbl,
            [
                'likes' => 'likes + ?' . $count,
            ],
            [
                'id' => $productId,
            ]
        );
    }
    public function updateProductSalesRegionStatus($salesRegionId, $status = 0)
    {
        return $this->db->update(
            'productsalesregion',
            [
                'status' => $status,
            ],
            [
                'id' => $salesRegionId,
            ]
        );
    }
    public function updateProductCategoryStatus(
        $productId,
        $categoryId,
        $status = 0
    ) {
        return $this->db->update(
            'productcategory',
            [
                'status' => $status,
            ],
            [
                'product_id' => $productId,
                'category_id' => $categoryId,
            ]
        );
    }
    public function updateProductRelationStatus($productRealtionId, $status = 0)
    {
        return $this->db->update(
            'productrelation',
            [
                'status' => $status,
            ],
            [
                'id' => $productRealtionId,
            ]
        );
    }
    public function updateProductSecurity($productId, $newPassword)
    {
        return $this->db->update(
            $this->maintbl,
            [
                'security' => $this->encrypt->encryptPassword($newPassword),
            ],
            [
                'id' => $productId,
            ]
        );
    }
    public function updateProductMediaStatus($productMediaId, $status = 0)
    {
        return $this->db->update(
            'productmedia',
            [
                'status' => $status,
            ],
            [
                'id' => $productMediaId,
            ]
        );
    }
    public function updateProductStatus($productId, $status = 2)
    {
        return $this->db->update(
            $this->mtablename,
            [
                'status' => $status,
            ],
            [
                'id' => $productId,
            ]
        );
    }
    public function updateProductVisibility($productId, $visibility = 0)
    {
        return $this->db->update(
            $this->mtablename,
            [
                'visibility' => $visibility,
            ],
            [
                'id' => $productId,
            ]
        );
    }

    private function getproducts($fields = [])
    {
        return $this->db->query(
            $this->maintbl,
            array_merge($fields, [
                'products.id as productid',
                'products.name',
                'products.price',
                'products.views',
                'products.likes',
                'products.weight',
                'products.sku',
                'products.shortdescription',
                'products.longdescription',
                'products.createdat',
                'products.updatedat',
                '(
          SELECT COUNT(*) FROM orders WHERE orders.product_id = products.id
        )  AS totalorders',
            ])
        );
    }
    private function getproductcategories($fields = [])
    {
        return $this->db->query('productcategory', [
            'productcategory.id',
            'productcategory.category_id',
            'productcategory.product_id',
            'productcategory.status',
        ]);
        // $this->db->join('inner',
        //   [
        //     [
        //       "table"=>"categories",
        //       "field"=>"id"
        //     ],
        //     [
        //       "table"=>"productcategory",
        //       "field"=>"category_id"
        //     ]
        //   ]
        // );
    }
    private function getproductbylocation($status = 1)
    {
        $this->getproducts();
        $this->db->join('inner', [
            [
                'table' => 'productlocation',
                'field' => 'product_id',
            ],
            [
                'table' => 'products',
                'field' => 'id',
            ],
        ]);
        $this->db->join('right', [
            [
                'table' => 'deliveryaddress',
                'field' => 'id',
            ],
            [
                'table' => 'productlocation',
                'field' => 'address_id',
            ],
        ]);
        $this->db->join('right', [
            [
                'table' => 'productsalesregion',
                'field' => 'product_id',
            ],
            [
                'table' => 'products',
                'field' => 'id',
            ],
        ]);
    }
    private function sortproducts($sort = [])
    {
        $limit = isset($sort['limit']) ? $sort['limit'] : 30;
        $pageno = isset($sort['pageno']) ? $sort['pageno'] : 1;
        $offset = ($pageno - 1) * $limit;
        isset($sort['order_rand']) ? $this->db->order_rand() : null;
        $this->db->limit($limit);
        $this->db->offset($offset);
        // isset($sort['order_by']) && isset($sort['order_desc']) ? $this->db->order_by_desc(($sort['order_tbl'] ??  '') $sort['order_tbl']),$sort['order_by']) : null;
        // isset($sort['order_by']) && isset($sort['order_asc']) ? $this->db->order_by_asc(($sort['order_tbl'] ??  '',$sort['order_by']) : null;
    }

    public function getProductsById($productId, $status = 1)
    {
        $this->getproducts();
        $this->db->where_equal('id', $productId);
        $this->db->and_where('status', $status);
        return $this->db->exec()->row;
    }

    /**
     * get product usinf its public Id
     *
     * @param String $productId Public Id of the product
     * @param String $status 1 = opened, 0 = closed to public
     * @return product Object
     **/
    public function getProductByProduct($productId, $status = 1)
    {
        return $this->getproducts()
            ->where_equal('product_id', $productId)
            ->and_where('status', $status)
            ->exec()->row;
    }

    public function getProductByUser($productId, $status = 1)
    {
        $fields = [
            ' (SELECT mediaurl FROM productmedia WHERE productmedia.product_id = products.id AND productmedia.mediatype = 1 AND productmedia.TYPE = 1  AND productmedia.status = 1 LIMIT 1 ) AS imageurl',
            ' (SELECT storeid FROM msellers WHERE products.mseller_id = msellers.id )AS storeid',
        ];
        $this->getproducts($fields);
        $this->db->where_equal('id', $productId);
        $this->db->and_where('status', $status);
        // $this->db->echoSql();
        return $this->db->exec()->row;
    }

    public function getProductBySeller($productId, $status = 1)
    {
        $fields = [
            '(
        SELECT COUNT(*) FROM orders INNER JOIN delivery ON orders.delivery_id = delivery.id WHERE delivery.delivery_status = ? AND orders.product_id = products.id
      ) AS completedorders',
            '(
        SELECT COUNT(*) FROM orders INNER JOIN delivery ON orders.delivery_id = delivery.id WHERE delivery.delivery_status = ? AND orders.product_id = products.id
      ) AS transitorders',
            '(
        SELECT COUNT(*) FROM orders INNER JOIN delivery ON orders.delivery_id = delivery.id WHERE delivery.delivery_status = ? AND orders.product_id = products.id
      ) AS pendingorders',
        ];
        $this->getproducts($fields);
        $this->db->where_equal('id', $productId);
        $this->db->and_where('status', $status);
        return $this->db->exec()->rows;
    }

    public function getRandomProducts($sort = [], $status = 1)
    {
        $this->getproducts();
        $this->sortproducts(
            array_merge($sort, ['order_by' => 'RAND()', 'order_asc' => true])
        );
        $this->db->where_equal('products.status', $status);
        $this->db->and_where('products.visibility', 1);
        return $this->db->exec()->rows;
    }

    public function getCategoryProducts(
        $categoryId,
        $visibility = 1,
        $sort = [],
        $pStatus = 1,
        $pcStatus = 1
    ) {
        $fields = [
            ' (SELECT mediaurl FROM productmedia WHERE productmedia.product_id = products.id AND productmedia.mediatype = 1 AND productmedia.TYPE = 1  AND productmedia.status = 1 LIMIT 1 ) AS imageurl',
            ' (SELECT storeid FROM msellers WHERE products.mseller_id = msellers.id )AS storeid',
        ];
        $this->getproducts($fields);
        $this->db->join('inner', [
            [
                'table' => 'productcategory',
                'field' => 'product_id',
            ],
            [
                'table' => 'products',
                'field' => 'id',
            ],
        ]);
        $this->db->where_equal('products.status', $pStatus);
        $this->db->and_where('products.visibility', $visibility);
        $this->db->and_where('productcategory.status', $pcStatus);
        $this->db->and_where('productcategory.category_id', $categoryId);
        $this->sortproducts(array_merge($sort, ['order_rand' => true]));

        // $this->db->echoSql();
        return $this->db->exec()->rows;
    }

    public function getCategoryProductsByPrice(
        $categoryId,
        $visibility = 1,
        $sort,
        $status = 1
    ) {
        $this->getproducts();
        $this->sortproducts(
            array_merge($sort, ['order_by' => 'price', 'order_asc' => true])
        );
        $this->db->join('inner', [
            [
                'table' => 'productcategory',
                'field' => 'product_id',
            ],
            [
                'table' => 'products',
                'field' => 'id',
            ],
        ]);
        $this->db->where_equal('products.status', $status);
        $this->db->and_where('productcategory.status', $status);
        $this->db->and_where('products.visibility', $visibility);
        $this->db->and_where('productcategory.category', $categoryId);
        return $this->db->exec()->rows;
    }

    public function getCategoryProductsByPriceAmount(
        $categoryId,
        $amount,
        $status = 1
    ) {
    }
    public function getStoreProducts($sellerId, $status = 1)
    {
        $fields = [
            ' (SELECT mediaurl FROM productmedia WHERE productmedia.product_id = products.id AND productmedia.mediatype = 1 AND productmedia.TYPE = 1  AND productmedia.status = 1 LIMIT 1 ) AS imageurl',
            ' (SELECT storeid FROM msellers WHERE products.mseller_id = msellers.id )AS storeid',
        ];
        $this->getproducts($fields);
        $this->db->where_equal('mseller_id', $sellerId);
        $this->db->and_where('status', $status);
        return $this->db->exec()->rows;
    }
    public function getSellerProducts($sellerId, $visibility = 1, $status = 1)
    {
        $fields = [
            ' (SELECT mediaurl FROM productmedia WHERE productmedia.product_id = products.id AND productmedia.mediatype = 1 AND productmedia.TYPE = 1  AND productmedia.status = 1 LIMIT 1 ) AS imageurl',
            ' (SELECT storeid FROM msellers WHERE products.mseller_id = msellers.id )AS storeid',
        ];
        $this->getproducts($fields);
        $this->db->where_equal('mseller_id', $sellerId);
        $this->db->and_where('products.visibility', $visibility);
        $this->db->and_where('status', $status);
        return $this->db->exec()->rows;
    }

    public function getTrendingProducts()
    {
    }

    public function getBestSellerProducts($status = 1)
    {
    }

    public function getHighestSaleProducts($sort = [])
    {
        $this->getproducts();
        $this->db->join('inner', [
            [
                'table' => 'orders',
                'field' => 'product_id',
            ],
            [
                'table' => 'products',
                'field' => 'id',
            ],
        ]);
        $this->db->where_equal('mseller_id', $sellerId);
        $this->db->and_where('status', $status);
        $this->db->and_where('products.visibility', $visibility);
        $sort['order_by'] =
            'SELECT COUNT(*) FROM orders WHERE product_id = products.id';
        $sort['order_desc'] = true;
        $this->sortproducts($sort);
        return $this->db->exec()->rows;
    }

    public function getProductRelationId($product1Id, $product2Id, $status = 1)
    {
        $this->db->query('productrelation', ['id']);
        $this->db->where_equal('(product1_id = ? ', $product1Id);
        $this->db->and_where('product2_id) = ?', $product2Id);
        $this->db->or_where('(product1_id = ?', $product1Id);
        $this->db->and_where('product2_id) = ?', $product2Id);
        $this->db->and_where('status = ?', $status);
        return $this->db->exec()->row;
    }
    public function getRelatedProducts($productId, $visibility = 1)
    {
        $this->getproducts();
        $sql =
            '(SELECT IF( product1_id = products.id,product2_id,product1_id) FROM productrelation WHERE (product1_id = products.id) OR (product2_id = products.id) AND products.id = ?)';
        $this->db->and_where_in('products.id ', $sql, [$productId]);
        $this->db->and_where('products.visibility', $visibility);
        return $this->db->exec()->rows;
    }
    public function getPurchasedWithProducts(
        $productId,
        $visibility = 1,
        $status = 1
    ) {
        $this->getproducts();
        $this->db->where_equal('status', $status);
        $sql =
            '(SELECT DISTINCT product_id FROM orders WHERE checkout_id IN (SELECT checkout_id FROM orders WHERE product_id = products.id) AND product_id <> ?)';
        $this->db->and_where_in('products.id ', $sql, [$productId]);
        $this->db->and_where('products.visibility', $visibility);
        return $this->db->exec()->rows;
    }

    public function getProductCategory($productId, $status = 1)
    {
        $this->getproductcategories();
        $this->db->where_equal('productcategory.status', $status);
        $this->db->and_where('product_id', $productId);
        return $this->db->exec()->rows;
    }

    private function getproductmedias()
    {
        return $this->db->query('productmedia', [
            'mediaurl',
            'IF(mediatype = 1,"image","video") AS mediatype',
            'IF(type = 1,"main","sub") AS type',
            'id as mediaid',
        ]);
    }

    public function getProductMedia($productId, $status = 1)
    {
        $this->getproductmedias();
        $this->db->where_equal('status', $status);
        $this->db->and_where('product_id', $productId);
        return $this->db->exec()->rows;
    }

    public function getProductsByManufacturer(
        $manufacturerId,
        $visibility = 1,
        $status = 1
    ) {
        $this->getproducts();
        $this->db->where_equal('status', $status);
        $this->db->and_where('manufacturer_id', $manufacturerId);
        $this->db->and_where('products.visibility', $visibility);
        return $this->db->exec()->rows;
    }

    public function getProductsByBrand($brandId, $visibility = 1, $status = 1)
    {
        $this->getproducts();
        $this->db->where_equal('status', $status);
        $this->db->and_where('brand_id', $brandId);
        $this->db->and_where('products.visibility', $visibility);
        return $this->db->exec()->rows;
    }
    private function getproductsalesregions()
    {
        return $this->db->query('productsalesregion', [
            'id',
            'product_id',
            'region_id',
            'country_id',
        ]);
    }

    public function getProductSalesRegion($productId, $status = 1)
    {
        $this->getproductsalesregions();
        $this->db->where_equal('status', $status);
        $this->db->and_where('product_id', $productId);
        return $this->db->exec()->rows;
    }

    public function getProductBySalesRegion(
        $regionId,
        $visibility = 1,
        $status = 1
    ) {
        $this->getproducts();
        $this->sortproducts(array_merge($sort, ['order_rand' => true]));
        $this->db->join('inner', [
            [
                'table' => 'productsalesregion',
                'field' => 'region_id',
            ],
            [
                'table' => 'products',
                'field' => 'id',
            ],
        ]);
        $this->db->where_equal('products.status', $status);
        $this->db->and_where('productsalesregion.status', $status);
        $this->db->and_where('productsalesregion.region_id', $regionId);
        return $this->db->exec()->rows;
    }
    public function getProductBySalesCountry(
        $countryId,
        $visibility = 1,
        $sort = [],
        $status = 1
    ) {
        $this->getproducts();
        $this->sortproducts(array_merge($sort, ['order_rand' => true]));
        $this->db->join('inner', [
            [
                'table' => 'productsalesregion',
                'field' => 'region_id',
            ],
            [
                'table' => 'products',
                'field' => 'id',
            ],
        ]);
        $this->db->where_equal('products.status', $status);
        $this->db->and_where('productsalesregion.status', $status);
        $this->db->and_where('productsalesregion.country_id', $countryId);
        $this->db->and_where('products.visibility', $visibility);
        return $this->db->exec()->rows;
    }

    public function getProductByLocations(
        $regionId,
        $visibility = 1,
        $status = 1,
        $sort = []
    ) {
        $this->getproductbylocation();
        $this->db->where_equal('products.status', $status);
        $this->db->and_where('deliveryaddress.city_id', $regionId);
        $this->db->and_where('products.visibility', $visibility);
        $this->sortproducts($sort);
        return $this->db->exec()->rows;
    }
    public function getProductByCategoryLocation(
        $categoryId,
        $regionId,
        $visibility = 1,
        $status = 1,
        $sort = []
    ) {
        $this->getproductbylocation();
        $this->db->join('inner', [
            [
                'table' => 'productcategory',
                'field' => 'product_id',
            ],
            [
                'table' => 'products',
                'field' => 'id',
            ],
        ]);
        $this->db->where_equal('products.status', $status);
        $this->db->and_where('deliveryaddress.city_id', $regionId);
        $this->db->and_where('productcategory.status', $status);
        $this->db->and_where('products.visibility', $visibility);
        $this->db->and_where('productcategory.category_id', $categoryId);
        $this->sortproducts($sort);
        return $this->db->exec()->rows;
    }
    public function getProductByLocationAndSalesRegion(
        $locationRegionId,
        $regionId,
        $visibility = 1,
        $status = 1,
        $sort = []
    ) {
        $this->getproductbylocation();
        $this->db->where_equal('products.status', $status);
        $this->db->and_where('deliveryaddress.city_id', $locationRegionId);
        $this->db->and_where('productsalesregion.status', $status);
        $this->db->and_where('productsalesregion.region_id', $regionId);
        $this->db->and_where('products.visibility', $visibility);
        $this->sortproducts($sort);
        return $this->db->exec()->rows;
    }
    public function getProductByLocationAndSalesCountry(
        $locationRegionId,
        $countryId,
        $visibility = 1,
        $status = 1,
        $sort = []
    ) {
        $this->getproductbylocation();
        $this->db->where_equal('products.status', $status);
        $this->db->and_where('deliveryaddress.city_id', $locationRegionId);
        $this->db->and_where('productsalesregion.status', $status);
        $this->db->and_where('productsalesregion.country_id', $countryId);
        $this->db->and_where('products.visibility', $visibility);
        $this->sortproducts($sort);
        return $this->db->exec()->rows;
    }
    public function getProductByCategoryLocationAndSalesRegion(
        $categoryId,
        $locationRegionId,
        $visibility = 1,
        $regionId,
        $status = 1,
        $sort = []
    ) {
        $this->getproductbylocation();
        $this->db->join('inner', [
            [
                'table' => 'productcategory',
                'field' => 'product_id',
            ],
            [
                'table' => 'products',
                'field' => 'id',
            ],
        ]);
        $this->db->where_equal('products.status', $status);
        $this->db->and_where('deliveryaddress.city_id', $locationRegionId);
        $this->db->and_where('productsalesregion.status', $status);
        $this->db->and_where('productsalesregion.region_id', $regionId);
        $this->db->and_where('productcategory.category_id', $categoryId);
        $this->db->and_where('products.visibility', $visibility);
        $this->sortproducts($sort);
        return $this->db->exec()->rows;
    }
    public function getProductByCategoryLocationAndSalesCountry(
        $categoryId,
        $locationRegionId,
        $countryId,
        $visibility = 1,
        $status = 1,
        $sort = []
    ) {
        $this->getproductbylocation();
        $this->db->join('inner', [
            [
                'table' => 'productcategory',
                'field' => 'product_id',
            ],
            [
                'table' => 'products',
                'field' => 'id',
            ],
        ]);
        $this->db->where_equal('products.status', $status);
        $this->db->and_where('deliveryaddress.city_id', $locationRegionId);
        $this->db->and_where('productsalesregion.status', $status);
        $this->db->and_where('productsalesregion.country_id', $countryId);
        $this->db->and_where('productcategory.category_id', $categoryId);
        $this->db->and_where('products.visibility', $visibility);
        $this->sortproducts($sort);
        return $this->db->exec()->rows;
    }
    public function getProductByCategorySalesRegion(
        $categoryId,
        $regionId,
        $visibility = 1,
        $status = 1,
        $sort = []
    ) {
        $this->getproductbylocation();
        $this->db->join('inner', [
            [
                'table' => 'productcategory',
                'field' => 'product_id',
            ],
            [
                'table' => 'products',
                'field' => 'id',
            ],
        ]);
        $this->db->where_equal('products.status', $status);
        $this->db->and_where('productsalesregion.status', $status);
        $this->db->and_where('productsalesregion.region_id', $regionId);
        $this->db->and_where('productcategory.category_id', $categoryId);
        $this->db->and_where('products.visibility', $visibility);
        $this->sortproducts($sort);
        return $this->db->exec()->rows;
    }
    public function getProductByCategorySalesCountry(
        $categoryId,
        $countryId,
        $visibility = 1,
        $status = 1,
        $sort = []
    ) {
        $this->getproductbylocation();
        $this->db->join('inner', [
            [
                'table' => 'productcategory',
                'field' => 'product_id',
            ],
            [
                'table' => 'products',
                'field' => 'id',
            ],
        ]);
        $this->db->where_equal('products.status', $status);
        $this->db->and_where('productsalesregion.status', $status);
        $this->db->and_where('productsalesregion.country_id', $countryId);
        $this->db->and_where('productcategory.category_id', $categoryId);
        $this->db->and_where('products.visibility', $visibility);
        $this->sortproducts($sort);
        return $this->db->exec()->rows;
    }
    public function getProductSecurity($productId, $status)
    {
        $this->db->query($this->maintbl, ['security']);
        $this->db->where_equal('products.id', $productId);
        $this->db->and_where('products.status', $status);
        return $this->db->exec()->rows;
    }
}
