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
        'total_price',
        'order_status'
    ];

    protected array $fillablesOrdersItem = [
        'order_id',
        'product_id',
        'quantity',
        'price'
    ];

    public function createOrders(array $columnsValues, int $userId)
    {
        $queryString = "INSERT INTO {$this->tableName} (user_id, order_id, total_price, order_status)
                    VALUES (:user_id, :order_id, :total_price, :order_status)
                    ON DUPLICATE KEY UPDATE 
                        user_id = :user_id,
                        order_id = :order_id,
                        total_price = :total_price,
                        order_status = :order_status;";

        $stmt = $this->connection->prepare($queryString);

        $userId = $_SESSION['logged_in']['id'];
        $order_id = time();

        $params = [
            ':user_id' => $userId,
            ':order_id' => $order_id,
            ':total_price' => $columnsValues['total_price'],
            ':order_status' => $columnsValues['order_status'] ?? 'függőben', // Alapértelmezett érték
        ];

        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindValue(':total_price', $columnsValues['total_price'], PDO::PARAM_STR);
        $stmt->bindValue(':order_status', $params[':order_status'], PDO::PARAM_STR); // Kösd be az order_status-t

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

    public function filterFillablesOrdersItem(array $data)
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $this->fillablesOrdersItem)) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
