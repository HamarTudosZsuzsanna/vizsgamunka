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
        'total_price',
    ];

    public function createOrders(array $columnsValues, int $userId)
    {
        $queryString = "INSERT INTO {$this->tableName} (user_id, total_price)
                    VALUES (:user_id, :total_price)
                    ON DUPLICATE KEY UPDATE 
                        user_id = :user_id,
                        total_price = :total_price;
                        ";

        $stmt = $this->connection->prepare($queryString);

        $params = [
            ':user_id' => $userId,
            ':total_price' => $columnsValues['total_price'],
        ];

        // Bind parameters with the correct type
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

        // Check if total_price is numeric and bind appropriately
        if (is_numeric($columnsValues['total_price'])) {
            if (is_int($columnsValues['total_price'])) {
                $stmt->bindValue(':total_price', (int)$columnsValues['total_price'], PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':total_price', (float)$columnsValues['total_price'], PDO::PARAM_STR);
            }
        } else {
            // Handle cases where total_price is not a valid number
            throw new InvalidArgumentException("The total_price value must be a valid number.");
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

    public function addOrderItem($itemData)
    {
        // SQL lekérdezés a rendelési tétel beszúrásához
        // Példa:
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price, total_price) VALUES (:order_id, :product_id, :quantity, :price, :total_price)";

        // Készíts egy PDO-statement-et és bindold a paramétereket
        // Végül végrehajtod a lekérdezést
    }
}
