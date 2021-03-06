<?php
class GCProduct extends GController
{
    public function add()
    {
        $seller = $this->load->helper('auth/permission', 'isSeller');
        $data = $this->load->helper('validators/product', 'add');

        $this->load->model('product');
        $insert = $this->model_product->addProduct(
            $seller['msellerid'],
            $data['productname'],
            $data['price'],
            $data['quantity'],
            $data['shortdescription'],
            $data['type'],
            $data['visibility']
        );
        if (!$insert) {
            $response = [
                'error' => true,
                'message' => 'An error occurred adding product',
                'code' => '500',
            ];
        } else {
            $response = [
                'status' => true,
                'message' => 'Product successfully created',
                'data' => ['prodtctid' => $insert],
            ];
        }
        $this->request->emit($response);
    }
    public function updateProductProfile(
        $sellerTradeId,
        $productId,
        $price,
        $shortdescr,
        $longDescription,
        $manufacturerId,
        $brand,
        $weight
    ) {
    }

    public function updateSalesRegion($tradeId, $productId, $regions)
    {
        $this->load->model('products');
        $productExist = $this->model_products->getProductsById($productId);
        if (!$productExist['status']) {
            return ['error' => true, 'message' => 'Product not found'];
        }
        $suppliedRegions = $regions;
        $activeregions = $this->model_products->getProductSalesRegion(
            $productId
        );
        $activeregions = $activeregions['status'] ? $activeregions['data'] : [];
        $countries = [];
        $openedregions = [];
        foreach ($regions as $region) {
            if ($region['regionid'] == 0) {
                array_push($countries, $region['countryid']);
            } else {
                array_push($openedregions, $region['regionid']);
            }
        }
        foreach ($activeregions as $active) {
            if ($active['region_id'] == 0) {
                if (!in_array($active['country_id'], $countries)) {
                    $this->model_products->updateProductSalesRegionStatus(
                        $active['id'],
                        0
                    );
                }
                in_array($active['country_id'], $countries)
                    ? array_splice(
                        $countries,
                        array_search($active['country_id'], $countries),
                        1
                    )
                    : null;
            } else {
                if (!in_array($active['region_id'], $openedregions)) {
                    $this->model_products->updateProductSalesRegionStatus(
                        $active['id'],
                        0
                    );
                }
                in_array($active['region_id'], $openedregions)
                    ? array_splice(
                        $openedregions,
                        array_search($active['region_id'], $openedregions),
                        1
                    )
                    : null;
            }
        }

        $closedregions = $this->model_products->getProductSalesRegion(
            $productId,
            0
        );
        $closedregions = $closedregions['status'] ? $closedregions['data'] : [];
        foreach ($closedregions as $closed) {
            if ($closed['region_id'] == 0) {
                if (in_array($closed['country_id'], $countries)) {
                    $this->model_products->updateProductSalesRegionStatus(
                        $closed['id'],
                        1
                    );
                }
                array_splice(
                    $countries,
                    array_search($closed['country_id'], $countries),
                    1
                );
            } else {
                echo $closed['region_id'];
                if (in_array($closed['region_id'], $openedregions)) {
                    $this->model_products->updateProductSalesRegionStatus(
                        $closed['id'],
                        1
                    );
                }
                in_array($closed['region_id'], $openedregions)
                    ? array_splice(
                        $openedregions,
                        array_search($closed['region_id'], $openedregions),
                        1
                    )
                    : null;
            }
        }
        var_dump($countries, $openedregions);
        $this->load->model('global');
        $i = 0;
        foreach ($openedregions as $region) {
            /**INSERT NEW REGION */
            $regionExist = $this->model_global->getRegion($region);
            $regionIndex = $this->getArrayIndexKey(
                $regions,
                'regionid',
                $region
            );
            $countryId = $regions[$regionIndex]['countryid'];
            if (
                $regionExist['status'] &&
                $regionExist['data']['country_id'] == $countryId
            ) {
                $this->model_products->addProductSalesRegion(
                    $productId,
                    $region,
                    $countryId
                );
            }
            array_splice($regions, $i, 1);
            $i++;
        }
        foreach ($countries as $country) {
            $this->model_products->addProductSalesRegion(
                $productId,
                0,
                $country
            );
        }
        // return $regions != $suppliedRegions ?  ["status"=>true,"message"=>"Product Sale Regions updated","data"=>["invalid"=>$regions]]  : [ "error"=>true,"message"=>"Unknown regions supplied!"];
        return [
            'status' => true,
            'message' => 'Product Sale Regions updated',
            'data' => ['invalid' => $regions],
        ];
    }

    private function getArrayIndexKey($array, $field, $value)
    {
        foreach ($array as $key => $item) {
            if ($item[$field] == $value) {
                return $key;
            }
        }
        return -1;
    }

    public function updateProductCategory($tradeId, $productId, $categories)
    {
        $this->load->model('products');
        $productExist = $this->model_products->getProductsById($productId);
        if (!$productExist['status']) {
            return ['error' => true, 'message' => 'Product not found'];
        }
        $categoryUpdate = $categories;
        $opened = $this->model_products->getProductCategory($productId);
        $opened =
            isset($opened['status']) && $opened['status']
                ? $opened['data']
                : [];
        foreach ($opened as $category) {
            if (!in_array($category['category_id'], $categories)) {
                $this->model_products->updateProductCategoryStatus(
                    $productId,
                    $category['category_id'],
                    0
                );
            }
            in_array($category['category_id'], $categories)
                ? array_splice(
                    $categories,
                    array_search($category['category_id'], $categories),
                    1
                )
                : null;
        }

        $closed = $this->model_products->getProductCategory($productId, 0);
        $closed =
            isset($closed['status']) && $closed['status']
                ? $closed['data']
                : [];
        foreach ($closed as $category) {
            if (in_array($category['category_id'], $categories)) {
                $this->model_products->updateProductCategoryStatus(
                    $productId,
                    $category['category_id'],
                    1
                );
                array_splice(
                    $categories,
                    array_search($category['category_id'], $categories),
                    1
                );
            }
        }
        $this->load->model('category');
        foreach ($categories as $categoryId) {
            $categoryExist = $this->model_category->getCategoryById(
                $categoryId
            );
            if ($categoryExist['status']) {
                $this->model_products->addProductCategory(
                    $productId,
                    $categoryId
                );
                in_array(array_search($categoryId, $categories))
                    ? array_splice(
                        $categories,
                        array_search($categoryId, $categories),
                        1
                    )
                    : null;
            }
        }
        return $categories != $categoryUpdate
            ? [
                'status' => true,
                'message' => 'Categories updated',
                'data' => ['invalid' => $categories],
            ]
            : ['error' => true, 'message' => 'Invalid Categories!'];
    }
    public function updateProductMainImage($tradeId, $productId, $imageId)
    {
    }
    public function deleteProduct($tradeId, $productId)
    {
        $this->load->model('product');
        $productExist = $this->model_products->getProductsById($productId);
        if (!$productExist['status']) {
            return ['error' => true, 'message' => 'Product not found'];
        }
        $deleted = $this->model_products->updateProductStatus($productId, 0);
        if ($deleted['status']) {
            return [
                'status' => true,
                'message' => 'Product deleted successfully!',
            ];
        }
        return ['error' => true, 'message' => 'An error was encountered!'];
    }
    public function uploadProductImages($tradeId, $productId)
    {
        $this->load->model('product');
        $productExist = $this->model_products->getProductsById($productId);
        if (!$productExist['status']) {
            return ['error' => true, 'message' => 'Product not found'];
        }
    }
    public function updateProductVisibility($tradeId, $productId, $visibility)
    {
        $this->load->model('product');
        $productExist = $this->model_products->getProductsById($productId);
        if (!$productExist['status']) {
            return ['error' => true, 'message' => 'Product not found'];
        }
        $updated = $this->model_products->updateProductVisibility(
            $productId,
            $visibility
        );
        if ($updated['status']) {
            return [
                'status' => true,
                'message' => 'Product visibilty updated successfully!',
            ];
        }
        return [
            'error' => true,
            'message' => 'An error occured while performing the action!',
        ];
    }
    public function updateProductAudience($tradeId, $productId, $audience)
    {
    }
    public function updateProductLocation($tradeId, $productId, $regions)
    {
    }
    public function updateProductViews($productId)
    {
        $this->load->model('products');
        $productExist = $this->model_products->getProductsById($productId);
        if (!$productExist['status']) {
            return ['error' => true, 'message' => 'Product not found'];
        }
        $updated = $this->model_products->updateProductViews($productId);
        if ($updated['status']) {
            return ['status' => true, 'message' => 'Product views updated!'];
        }
        return ['error' => true, 'message' => 'Error updating views'];
    }
    public function updateProductLikes($productId)
    {
        $this->load->model('products');
        $productExist = $this->model_products->getProductsById($productId);
        if (!$productExist['status']) {
            return ['error' => true, 'message' => 'Product not found'];
        }
        $updated = $this->model_products->updateProductLikes($productId);
        if ($updated['status']) {
            return ['status' => true, 'message' => 'Product likes updated!'];
        }
        return ['error' => true, 'message' => 'Error updating likes'];
    }
    public function removeProductMedia($tradeId, $productId, $imageId)
    {
        $this->load->model('products');
        $productExist = $this->model_products->getProductsById($productId);
        if (!$productExist['status']) {
            return ['error' => true, 'message' => 'Product not found'];
        }
        $deleted = $this->model_products->updateProductMediaStatus(
            $imageIdc,
            0
        );
        if ($deleted['status']) {
            return [
                'status' => true,
                'message' => 'Product deleted successfully!',
            ];
        }
    }
    public function addProductReviews($tradeId, $productId, $review)
    {
    }
    public function removeProductReview($tradeId, $productId, $reviewId)
    {
    }

    public function getProductInfoUser($productId)
    {
        $this->load->model('products');
        $productExist = $this->model_products->getProductByUser($productId);
        if (!$productExist['status']) {
            return ['error' => true, 'message' => 'Product not found'];
        }
        $media = $this->model_products->getProductMedia($productId);
        $productExist['data']['media'] = $media['status'] ? $media['data'] : [];
        return [
            'status' => true,
            'message' => 'Products found',
            'data' => $productExist['data'],
        ];
    }
    public function getProductInfoSeller($productId)
    {
        $this->load->model('products');
        $productExist = $this->model_products->getProductBySeller($productId);
        if (!$productExist['status']) {
            return ['error' => true, 'message' => 'Product not found'];
        }
        return [
            'status' => true,
            'message' => 'Products found',
            'data' => $productExist['data'],
        ];
    }
    public function getProductInfoAdmin($productId)
    {
    }
    public function getStoreProducts($storeid)
    {
        $this->load->model('seller');
        $this->load->model('products');
        $sellerExists = $this->model_seller->getSellerbyStoreId($storeid);
        if (!$sellerExists['status']) {
            return [
                'error' => true,
                'message' => 'No seller account is associated with tradeId!',
            ];
        }
        $sellerProducts = $this->model_products->getSellerProducts(
            $sellerExists['data']['msellerid']
        );
        if (!$sellerProducts['status']) {
            return ['error' => true, 'message' => 'Seller has no products!'];
        }
        $i = 0;
        foreach ($sellerProducts['data'] as $item) {
            $media = $this->model_products->getProductMedia($item['id']);
            $sellerProducts['data'][$i]['media'] = $media['status']
                ? $media['data']
                : [];
            $i++;
        }
        return [
            'status' => true,
            'message' => 'Seller products found',
            'data' => $sellerProducts['data'],
        ];
    }

    public function getSellerProducts($productId)
    {
        $this->load->model('products');
        $productExist = $this->model_products->getProductsById($productId);
        if (!$productExist['status']) {
            return ['error' => true, 'message' => 'Product not found'];
        }
        $this->load->model('seller');
        $sellerProducts = $this->model_products->getSellerProducts(
            $productExist['data']['msellerid']
        );
        if (!$sellerProducts['status']) {
            return ['error' => true, 'message' => 'Seller has no products!'];
        }
        $i = 0;
        foreach ($sellerProducts['data'] as $item) {
            $media = $this->model_products->getProductMedia($item['id']);
            $sellerProducts['data'][$i]['media'] = $media['status']
                ? $media['data']
                : [];
            $i++;
        }
        return [
            'status' => true,
            'message' => 'Seller products found',
            'data' => $sellerProducts['data'],
        ];
    }
    public function getProductSellerInfo($productId)
    {
        $this->load->model('products');
        $productExist = $this->model_products->getProductsById($productId);
        if (!$productExist['status']) {
            return ['error' => true, 'message' => 'Product not found'];
        }
        $this->load->model('seller');
        $sellerFound = $this->model_seller->getSellerbyTradeId(
            $productExist['data']['msellerid']
        );
        return $sellerFound['status']
            ? [
                'status' => true,
                'message' => 'Product seller found!',
                'data' => $sellerFound['data'],
            ]
            : [
                'error' => true,
                'message' => 'An error was encountered while fetching data!',
            ];
    }
    public function getCategoryProducts($categoryId)
    {
        $this->load->model('products');
        $catProducts = $this->model_products->getCategoryProducts($categoryId);
        return $catProducts['status']
            ? [
                'status' => true,
                'message' => 'Category products found!',
                'data' => $catProducts['data'],
            ]
            : [
                'error' => true,
                'message' => 'An error was encountered while fetching data!',
            ];
    }
    public function getProductCategories($productId)
    {
    }

    public function getRelatedProducts($productId)
    {
        $this->load->model('product');
        $productExist = $this->model_products->getProductsById($productId);
        if (!$productExist['status']) {
            return ['error' => true, 'message' => 'Product not found'];
        }
        $relatedProducts = $this->model_products->getRelatedProducts(
            $productId
        );
        if (!$relatedProducts['status']) {
            $relatedProducts = $this->model_products->getPurchasedWithProducts(
                $productId
            );
        }
        if (!$relatedProducts['status']) {
            $relatedProducts = $this->model_products->getSellerProducts(
                $productExist['data']['msellerid']
            );
        }

        return $relatedProducts['status']
            ? [
                'status' => true,
                'message' => 'Related products found!',
                'data' => $relatedProducts['data'],
            ]
            : [
                'error' => true,
                'message' => 'An error was encountered while fetching data!',
            ];
    }

    // Products Requests

    public function addProductRequest()
    {
    }
    public function uploadProductRequestVisibility()
    {
    }
}
?>
