<?php
class GCSeller extends GController
{
    public function index()
    {
        $seller = $this->load->helper('auth/permission', 'isSeller');

        if ($seller) {
            $this->load->model('product');
            $seller['products'] =
                $this->model_product->getSellerProducts($seller['msellerid']) ?:
                [];
            $this->request->emit([
                'status' => true,
                'message' =>
                    'Store data retreived successfully. Happy Trading!',
                'data' => $seller,
            ]);
        }
    }
    public function register()
    {
        $user = $this->load->helper('auth/permission', 'isUser');
        $data = $this->load->helper('validators/seller', 'register');
        extract($data);
        $this->load->model('seller');

        $insert = $this->model_seller->addSeller(
            $user['userid'],
            $data['storename'],
            $data['storeid'],
            $data['cityid'],
            $data['countryid']
        );
        if (!$insert) {
            $this->request->emit([
                'error' => true,
                'message' =>
                    'An error was encountered. Please try again later!',
                'code' => 503,
            ]);
        }
        $this->request->emit([
            'status' => true,
            'message' => 'Store successfully created. Happy Trading!',
        ]);
    }
    public function updateSeller()
    {
        $this->load->model('seller');
        $vaild = $this->validateseller($storename, $address, $city, $country);
        if (!$valid['status']) {
            return $valid;
        }
        $sellerExists = $this->model_seller->getSellerbyTradeId($tradeId);
        if (!$sellerExists['status']) {
            return ['error' => true, 'message' => 'Seller account not found'];
        }
        $pdate = $this->model_seller->updateSeller(
            $sellerExists['data']['msellerid'],
            $storename,
            $address,
            $city,
            $country,
            $shortinfo,
            $slogan
        );
        if (!$update) {
            return [
                'error' => true,
                'message' => 'An error was encountered please try again later',
            ];
        } else {
            return [
                'status' => true,
                'message' => 'Seller profile successfully updated',
            ];
        }
    }

    public function updateshortinfo()
    {
        $seller = $this->load->helper('auth/permission', 'isSeller');
        $this->load->model('seller');

        $data = $this->request->post(['shortinfo'], true);

        $update = $this->model_seller->updateSellerShortInfo(
            $seller['msellerid'],
            $data['shortinfo']
        );

        if ($update) {
            $this->request->emit([
                'status' => true,
                'message' => 'Store info updated successfully',
            ]);
        } else {
            $this->request->emit([
                'error' => true,
                'message' => 'Update Failed',
                'code' => 500,
            ]);
        }
    }

    public function updateSellerCategory()
    {
        $this->load->model('seller');
        $sellerExists = $this->model_seller->getSellerbyTradeId($tradeId);
        if (!$sellerExists['status']) {
            return ['error' => true, 'message' => 'Seller account not found'];
        }
        $sellerCategories = $this->model_seller->getSellerCategory(
            $sellerExists['data']['msellerid'],
            1
        );
        $sellerCategories = isset($sellerCategories['data'])
            ? $sellerCategories['data']
            : [];
        $foundCategories = [];
        foreach ($sellerCategories as $item) {
            array_push($foundCategories, $item['category_id']);
        }
        $newCategories = array_diff($categories, $foundCategories);
        $removedCategories = array_diff($foundCategories, $categories);
        foreach ($removedCategories as $item) {
            $this->model_seller->updateSellerCategoryStatus(
                $sellerExists['data']['msellerid'],
                $item,
                0
            );
        }
        $closedCategories = $this->model_seller->getSellerCategory(
            $sellerExists['data']['msellerid'],
            0
        );
        $closedCategories = isset($closedCategories['data'])
            ? $closedCategories['data']
            : [];
        $closedItems = [];
        foreach ($closedCategories as $item) {
            array_push($closedItems, $item['category_id']);
        }
        $reopenedItems = array_diff($categories, $closedItems);
        foreach ($reopenedItems as $item) {
            $this->model_seller->updateSellerCategoryStatus(
                $sellerExists['data']['msellerid'],
                $item,
                1
            );
        }
        foreach ($newCategories as $item) {
            $this->model_seller->addSellerCategory(
                $sellerExists['data']['msellerid'],
                $item
            );
        }
        return [
            'status' => true,
            'message' => 'Seller categories updated successfully!',
        ];
    }
    public function getSellerByStoreName($storename)
    {
        $this->load->model('seller');
        $sellerExists = $this->model_seller->getSellersByName($storename);
        if (!$sellerExists['status']) {
            return ['error' => true, 'message' => 'Store not found'];
        }
        return [
            'status' => true,
            'message' => 'Matching Stores Found!',
            'data' => $sellerExists['data'],
        ];
    }
    public function getSellerByCountry($countryId)
    {
        $this->load->model('seller');
        $sellerExists = $this->model_seller->getSellersByCountry($countryId);
        if (!$sellerExists['status']) {
            return ['error' => true, 'message' => 'Store not found'];
        }
        return [
            'status' => true,
            'message' => 'Matching Stores Found!',
            'data' => $sellerExists['data'],
        ];
    }
    public function getSellerByRegion($cityId)
    {
        $this->load->model('seller');
        $sellerExists = $this->model_seller->getSellersByCity($cityId);
        if (!$sellerExists['status']) {
            return ['error' => true, 'message' => 'Store not found'];
        }
        return [
            'status' => true,
            'message' => 'Matching Stores Found!',
            'data' => $sellerExists['data'],
        ];
    }
    public function getSellerByCategory($categoryId)
    {
        $this->load->model('seller');
        $sellerExists = $this->model_seller->getCategorySellers($categoryId);
        if (!$sellerExists['status']) {
            return ['error' => true, 'message' => 'Store not found'];
        }
        return [
            'status' => true,
            'message' => 'Matching Stores Found!',
            'data' => $sellerExists['data'],
        ];
    }
    public function getSellerCategories($tradeId)
    {
        $this->load->model('seller');
        $sellerExists = $this->model_seller->getSellerbyTradeId($tradeId);
    }
    public function getSellerByTradeId($tradeId)
    {
        $this->load->model('seller');
        $sellerExists = $this->model_seller->getSellerbyTradeId($tradeId);
        if (!$sellerExists['status']) {
            return ['error' => true, 'message' => 'Seller account not found'];
        }
        return [
            'status' => true,
            'message' => 'Seller account found',
            'data' => $sellerExists['data'],
        ];
    }
    public function updateStoreImage($tradeId)
    {
        $this->load->library('file');
        $this->library_file->uploadImage('sellers');
    }
    private function validateseller($storename, $storeid, $cityId, $countryId)
    {
        $this->load->library('validator');
        if (!$this->library_validator->username($storeid)) {
            return [
                'error' => true,
                'message' => 'Invalid Parameter',
                'field' => 'storeid',
            ];
        }
        if (!$this->library_validator->cleanString($storename, 1, 50)) {
            return [
                'error' => true,
                'message' => 'Invalid Parameter',
                'field' => 'Store name',
            ];
        }
        $storeIDExists = $this->model_seller->getSellersByStoreid($storeid);
        if ($storeIDExists['status']) {
            return [
                'error' => true,
                'message' => 'Store ID taken',
                'field' => 'storeid',
            ];
        }

        if (!$this->library_validator->int($cityId)) {
            return [
                'error' => true,
                'message' => 'Invalid Parameter',
                'field' => 'city',
            ];
        }
        if (!$this->library_validator->int($countryId)) {
            return [
                'error' => true,
                'message' => 'Invalid Parameter',
                'field' => 'country',
            ];
        }
        // $this->load->model('global');
        // $cityExists = $this->model_global->getCityById($cityId);
        // if($cityExists['status']) return ['error'=>true,'message'=>'Please select market verified city','field'=>'cityid'];
        // $countryExists = $this->model_global->getCountryById($countryId);
        // if($countryExists['status']) return ['error'=>true,'message'=>'Please select market verified country','field'=>'countryid'];
        return ['status' => true];
    }
}
?>
