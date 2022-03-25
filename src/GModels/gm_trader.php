<?php
class GMTrader extends GModel
{
  private $mtablename = 'mtraders';

  public function addTrader($userId, $firstname, $lastname, $othername, $gender, $cityId, $countryId, $registrationDate)
  {
    return $this->db->insert(
      $this->mtablename,
      array(
        'user_id' => $userId,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'othername' => $othername,
        'gender' => $gender,
        'city_id' => $cityId,
        'country_id' => $countryId,
        'registration_date' => $registrationDate
      )
    );
  }

  public function updateTrader($userId, $firstname, $lastname, $othername, $gender, $cityId, $countryId, $registrationDate)
  {
    return $this->db->update(
      $this->mtablename,
      [
        'firstname' => $firstname,
        'lastname' => $lastname,
        'othername' => $othername,
        'gender' => $gender,
        'city_id' => $cityId,
        'country_id' => $countryId,
        'registration_date' => $registrationDate
      ],
      [
        'user_id' => $userId
      ]
    );
  }

  private function gettraders($fields = [])
  {
    $this->db->query($this->mtablename, array_merge($fields, ["mtraders.id AS mtraderid", "mtraders.firstname", "mtraders.lastname", "mtraders.othername", "gender", "city_id AS cityid", "city.country_id AS countryid", "city.name AS city", "country.name AS country", "username", "email", "telephone", "trade_id", "emailverified", "telephoneverified"]));

    $this->db->join('inner', [["table" => "users", "field" => "id"], ["table" => $this->mtablename, "field" => "user_id"]]);
    $this->db->join('left', [["table" => "city", "field" => "id"], ["table" => $this->mtablename, "field" => "city_id"]]);
    $this->db->join('left', [["table" => "country", "field" => "id"], ["table" => 'city', "field" => "country_id"]]);
  }

  public function getTraderByUserId($userid, $status = 1)
  {
    $this->gettraders(["IF((SELECT COUNT(user_id) FROM msellers WHERE user_id = mtraders.user_id),true,false) AS isseller", "IF((SELECT COUNT(user_id) FROM mcouriers WHERE user_id = mtraders.user_id),true,false) AS iscourier",]);
    $this->db->where_equal("status", $status, '', 'users')->and_where("user_id", $userid, $this->mtablename);
    return $this->db->exec()->row;
  }

  public function getTraderByTradeId($traderId, $status = 1)
  {
    $this->gettraders([]);
    $this->db->where_equal("status", $status, '', 'users');
    $this->db->and_where("trade_id", $traderId, "users");
    return $this->db->exec()->row;
  }

  private function getcarts($fields = [])
  {
    return $this->db->query("cart", array_merge($fields, ["id AS cartid", "product_id", "quantity", "IFNULL((SELECT amount FROM deals WHERE mtrader_id = ? AND product_id = ? AND status = ?),(SELECT price FROM products WHERE product_id = ?)) AS price"]));
  }

  public function addCart($mtraderId, $productId, $quantity)
  {
    return $this->dbcrud->insert(
      'cart',
      array(
        'mtrader_id' => $mtraderId,
        'product_id' => $productId,
        'quantity' => $quantity
      )
    );
  }
  public function updateCart($cart_id, $quantity)
  {
    return $this->dbcrud->update(
      'cart',
      [
        'quantity' => $quantity,
      ],
      [
        'id' => $cart_id
      ]
    );
  }

  public function updateCartStatus($cartId, $status = 2)
  {
    return $this->dbcrud->update(
      'cart',
      [
        'status' => $status,
      ],
      [
        'id' => $cartId
      ]
    );
  }

  public function getTraderCart($mtraderid, $status = 1)
  {
    $this->getcarts();
    $this->db->where_equal("status", $status);
    $this->db->and_where("mtrader_id", $mtraderid);
    return $this->db->execute();
  }

  public function getcartData($cartId, $status = 1)
  {
    $this->getcarts();
    $this->db->where_equal("status", $status);
    $this->db->and_where("id", $cartId);
    return $this->db->exec()->row;
  }

  public function getCartTotal($mtraderId, $status = 1)
  {
    return $this->db->query("cart", ['SUM( IFNULL((SELECT amount FROM deals WHERE id = deal_id AND status = status),(SELECT price FROM products WHERE id = product_id )) * quantity ) AS total'])->addParam([$mtraderId])->where_equal("status", $status)->and_where("trade_id", $traderId)->exec()->row;
  }
}
