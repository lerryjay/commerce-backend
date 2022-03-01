<?php
class GHValidatorsProduct extends GHelpers
{
    public function add($params = [])
    {
        $params = $this->request->post([
            'productname',
            'shortdescription',
            'longdescription',
            'quantity',
            'price',
            'visibility',
            'type',
        ]);
        extract($params);
        if (!$this->validator->dirtyString($productname, 1, 50)) {
            $this->request->emit([
                'code' => 400,
                'error' => true,
                'message' => 'Invalid product name',
                'field' => 'productname',
            ]);
        }
        if (
            isset($shortdesciption) &&
            !$this->validator->dirtyString($shortdescription, 1, 250)
        ) {
            $this->request->emit([
                'error' => true,
                'code' => 400,
                'message' => 'Invalid short description',
                'field' => 'shortdescription',
            ]);
        }
        if (
            isset($longdesciption) &&
            !$this->validator->dirtyString($longdescription, 1, 250)
        ) {
            $this->request->emit([
                'error' => true,
                'message' => 'Invalid long description',
                'field' => 'longdescription',
            ]);
        }
        if (isset($quantity) && !$this->validator->int($quantity)) {
            $this->request->emit([
                'error' => true,
                'code' => 400,
                'message' => 'Quantity available can only be digits ',
                'field' => 'quantity',
            ]);
        }
        if (isset($price) && !$this->validator->amount($price)) {
            $this->request->emit([
                'error' => true,
                'code' => 400,
                'message' => 'Price can only be digits',
                'field' => 'price',
            ]);
        }
        if (isset($visibility) && !$this->validator->int($visibility)) {
            $this->request->emit([
                'error' => true,
                'code' => 400,
                'message' => 'Invalid Parameter',
                'field' => 'visibility',
            ]);
        }
        if (isset($type) && !$this->validator->int($type)) {
            $this->request->emit([
                'error' => true,
                'message' => 'Invalid Parameter',
                'field' => 'type',
            ]);
        }
        return $params;
    }
}
?>
