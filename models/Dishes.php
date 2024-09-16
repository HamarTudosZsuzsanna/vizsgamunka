<?php
require_once('Model.php');

class Dishes extends Model
{

    protected string $tableName = 'dishes';

    protected array $fillablesDishes = [
        'id',
        'dishes_name',
        'description',
        'price',
        'dishes_image',
        'admin_id',
        'categories',
    ];

    public function createDishes(array $columnsValues, int $adminId)
    {

        $queryString = "INSERT INTO {$this->tableName} (dishes_name, description, price, dishes_image, admin_id, categories)
                    VALUES (:dishes_name, :description, :price, :dishes_image, :admin_id, :categories)
                    ON DUPLICATE KEY UPDATE 
                        dishes_name = VALUES(dishes_name), 
                        description = VALUES(description), 
                        price = VALUES(price),
                        dishes_image = VALUES(dishes_image);
                        admin_id = VALUES(admin_id);
                        categories = VALUES(categories);";

        $stmt = $this->connection->prepare($queryString);

        $params = [
            ':admin_id' => $adminId,
            ':dishes_name' => $columnsValues['dishes_name'],
            ':description' => $columnsValues['description'],
            ':price' => $columnsValues['price'],
            ':dishes_image' => $columnsValues['dishes_image'],
            ':categories' => $columnsValues['categories']
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

    public function updateDishes(array $data, array $conditions): string|false
    {
        $data = parent::castValues($data);
        return parent::update($data, $conditions);
    }

    public function filterFillablesDishes(array $data)
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $this->fillablesDishes)) {
                unset($data[$key]);
            }
        }

        return $data;
    }


}
