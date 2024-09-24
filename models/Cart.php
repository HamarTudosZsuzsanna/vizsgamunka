<?php
require_once('Model.php');
require_once(TRAITS_DIR . '/MetaData.php');

class Cart extends Model
{
    use MetaData;

    protected string $tableName = 'cart_item';

    protected array $fillablesCart = [
        'id',
        'cart_user_id',
        'cart_product_id',
        'cart_quantity',
        'cart_price',
        'total_price',
    ];

    public function createCart(array $columnsValues, int $userId)
    {
        // A created_at mezőt már nem kell kezelnünk
        $queryString = "INSERT INTO {$this->tableName} (cart_user_id, cart_product_id, cart_quantity, cart_price, total_price)
                    VALUES (:cart_user_id, :cart_product_id, :cart_quantity, :cart_price, :total_price)
                    ON DUPLICATE KEY UPDATE 
                        cart_user_id = :cart_user_id,
                        cart_product_id = :cart_product_id,
                        cart_quantity = :cart_quantity,
                        cart_price = :cart_price,
                        total_price = :total_price;";

        $stmt = $this->connection->prepare($queryString);

        $params = [
            ':cart_user_id' => $userId,
            ':cart_product_id' => $columnsValues['cart_product_id'],
            ':cart_quantity' => $columnsValues['cart_quantity'],
            ':cart_price' => $columnsValues['cart_price'],
            ':total_price' => $columnsValues['total_price']
        ];

        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value, PDO::PARAM_STR);
        }

        if (!$stmt->execute()) {
            error_log("SQL Error: " . implode(", ", $stmt->errorInfo()));
            return false;
        }

        return true;
    }



    public function filterFillablesCart(array $data)
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $this->fillablesCart)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    public function deleteCartItem($cartItemId) {
        $conditions = [
            'id' => $cartItemId
        ];
        return $this -> delete($conditions);
    }

    public function clearCartByUserId($userId) {
        $query = "DELETE FROM cart_item WHERE cart_user_id = :cart_user_id";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':cart_user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

}
