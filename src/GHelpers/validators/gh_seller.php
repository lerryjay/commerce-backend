<?php
class GHValidatorsSeller extends GHelpers
{
    public function register($params)
    {
        extract($this->request->JSONPost(['storename', 'storeid'], true));
        if (!isset($storeid) || strlen($storeid) < 3) {
            $this->request->emit([
                'status' => false,
                'message' =>
                    'Please provide a unique storeid not less than 3 characters',
                'data' => ['field' => 'storeid'],
                'code' => 400,
            ]);
        }
        if (!isset($storename) || strlen($storename) < 1) {
            $this->request->emit([
                'status' => false,
                'message' => 'Please provide store name',
                'data' => ['field' => 'storename'],
                'code' => 400,
            ]);
        }
        $this->load->model('seller');
        if ($this->model_seller->getSellersByStoreid($storeid)) {
            $this->request->emit([
                'status' => false,
                'message' =>
                    'The storeid is already assigned to another seller',
                'data' => ['field' => 'storeid'],
                'code' => 403,
            ]);
        }
        return [
            'storeid' => $storeid,
            'storename' => $storename,
            'cityid' => null,
            'countryid' => null,
        ];
    }
}
