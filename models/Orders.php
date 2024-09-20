<?php
require_once('Model.php');
require_once(TRAITS_DIR . '/MetaData.php');

class Orders extends Model
{
    use MetaData;

    protected string $tableName = 'orders';

    protected array $fillablesOrders = [
        'id',
        'user_id',
        'order_id',
        'dish_id',
        'quantity',
        'price',
    ];

    public function createOrders(array $columnsValues, int $userId)
    {

        $queryString = "INSERT INTO {$this->tableName} (user_id, order_id, dish_id, quantity, price)
                    VALUES (:user_id, :order_id, :dish_id, :quantity, :price)
                    ON DUPLICATE KEY UPDATE 
                        user_id = :user_id,
                        order_id = :order_id,
                        dish_id = :dish_id,
                        quantity = :quantity,
                        price = :price;";

        $stmt = $this->connection->prepare($queryString);

        $params = [
            ':user_id' => $userId,
            ':order_id' => $columnsValues['order_id'],
            ':dish_id' => $columnsValues['dish_id'],
            ':quantity' => $columnsValues['quantity'],
            ':price' => $columnsValues['price']
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

    public function filterFillablesOrders(array $data)
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $this->fillablesOrders)) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
