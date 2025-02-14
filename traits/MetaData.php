<?php
trait MetaData
{
    public function createMeta(array $columnsValues, int $relationId)
    {

        $queryString = "INSERT INTO {$this->tableName}_meta (user_id, phone, address, number, accepted)
                    VALUES (:user_id, :phone, :address, :number, :accepted)
                    ON DUPLICATE KEY UPDATE 
                        phone = VALUES(phone), 
                        address = VALUES(address), 
                        number = VALUES(number),
                        accepted = VALUES(accepted);";

        $stmt = $this->connection->prepare($queryString);

        $params = [
            ':user_id' => $relationId,
            ':phone' => $columnsValues['phone'],
            ':address' => $columnsValues['address'],
            ':number' => $columnsValues['number'],
            ':accepted' => isset($columnsValues['accepted']) ? (int)$columnsValues['accepted'] : 0
        ];

        foreach ($params as $param => $value) {
            $type = ($param === ':accepted') ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($param, $value, $type);
        }

        return $stmt->execute();
    }

    public function createMetaOrders(array $columnsValues, int $userId)
{
    $queryString = "INSERT INTO orders_item (order_id, product_id, quantity, price)
                    VALUES (:order_id, :product_id, :quantity, :price)
                    ON DUPLICATE KEY UPDATE 
                        quantity = :quantity, 
                        price = :price;";

    $stmt = $this->connection->prepare($queryString);

    $params = [
        ':order_id' => $columnsValues['order_id'],
        ':product_id' => $columnsValues['product_id'],
        ':quantity' => $columnsValues['quantity'],
        ':price' => $columnsValues['price']
    ];

    return $stmt->execute($params);
}

    public function slugify($string)
    {

        $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

        $string = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);

        $string = trim($string, '-');

        $string = strtolower($string);

        return $string;
    }

    protected function readMeta(array $columns = ['*'],  array $conditions = [], $tableName)
    {
        $columnNames = implode(',', $columns);
        $conditionString = '';

        if (!empty($conditions)) {
            $conditionString = $this->assembleConditions($conditions);
        }

        $stmt = $this->connection->prepare("select $columnNames from $tableName $conditionString");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) === 1) {
            $result = reset($result);
        }

        return $result;
    }
}
