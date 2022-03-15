<?php
class GRUser extends GRoute
{
    public function index()
    {
        // echo 'worked';
        $this->load->library('encrypt');
        // echo $this->library_encrypt->generate_api_private_key();
    }

    private function register()
    {
        $postFields = $this->request->post([
            'username',
            'telephone',
            'email',
            'password',
        ]);
        $this->load->controller('user');
        $res = $this->controller_user->addUser(
            $postFields['username'],
            $postFields['email'],
            $postFields['telephone'],
            $postFields['password']
        );
        $this->request->emit($res);
    }

    public function login()
    {
        $postFields = $this->request->post(['loginid', 'password']);
        $this->load->controller('user');
        $res = $this->controller_user->loginUser(
            $postFields['loginid'],
            $postFields['password']
        );
        $this->request->emit($res);
    }

    public function balance()
    {
        $user = $this->load->controller('user');
        $trasaction = $this->load->controller('transactions');
        $userId = $this->request->sanitize('jire');
        $userId = $this->request->validateApiKey();
        // $ = $user->decodeApiKey($key);
        $balance = $trasaction->getUserBalance($userId);
        $this->request->emit([
            'status' => true,
            'balance' => $balance,
            'message' => "Your balance is $balance",
        ]);
    }

    public function textt()
    {
        $seller = $this->load->controller('seller');
        $seller->getSellerByName();
    }

    public function creditWallet()
    {
        $user = $this->load->controller('user');
        $trasaction = $this->load->controller('transactions');
    }
}
?>
