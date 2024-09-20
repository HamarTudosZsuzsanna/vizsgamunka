<?php
require_once('Model.php');
require_once(TRAITS_DIR . '/MetaData.php');

class Dishes extends Model
{
    use MetaData;

    protected string $tableName = 'dishes';

    protected array $fillablesDishes = [
        'id',
        'dishes_name',
        'description',
        'price',
        'dishes_image',
        'admin_id',
        'categories',
        'slug',
    ];

    public function createDishes(array $columnsValues, int $adminId)
    {

        $queryString = "INSERT INTO {$this->tableName} (dishes_name, description, price, dishes_image, admin_id, categories, slug)
                    VALUES (:dishes_name, :description, :price, :dishes_image, :admin_id, :categories, :slug)
                    ON DUPLICATE KEY UPDATE 
                        dishes_name = VALUES(dishes_name), 
                        description = VALUES(description), 
                        price = VALUES(price),
                        dishes_image = VALUES(dishes_image);
                        admin_id = VALUES(admin_id);
                        categories = VALUES(categories);
                        slug = VALUES(slug);";

        $stmt = $this->connection->prepare($queryString);

        $params = [
            ':admin_id' => $adminId,
            ':dishes_name' => $columnsValues['dishes_name'],
            ':description' => $columnsValues['description'],
            ':price' => $columnsValues['price'],
            ':dishes_image' => $columnsValues['dishes_image'],
            ':categories' => $columnsValues['categories'],
            ':slug' => $columnsValues['slug']
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

    public function updateDishes(array $data, string $dishesSlug): bool
    {
        $errors = [];

        if (!isset($_SESSION['logged_in']['id']) || !is_numeric($_SESSION['logged_in']['id']) || (int)$_SESSION['logged_in']['id'] <= 0) {
            $errors['form_errors'] = 'Hibás felhasználói azonosító.';
            return $errors;
        }

        $queryString = "UPDATE dishes SET dishes_name = :dishes_name, description = :description, price = :price, dishes_image = :dishes_image, categories = :categories WHERE slug = :slug";

        $params = [
            ':dishes_name' => $data['dishes_name'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':dishes_image' => $data['dishes_image'],
            ':categories' => $data['categories'],
            ':slug' => $dishesSlug
        ];

        $stmt = $this->connection->prepare($queryString);

        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value, PDO::PARAM_STR);
        }

        if (!$stmt->execute()) {
            error_log("SQL Error: " . implode(", ", $stmt->errorInfo()));
            return false;
        }

        return true;
    }

    public function getDishNameById($dishId)
    {
        $query = "SELECT dishes_name FROM dishes WHERE id = :dish_id";
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue(':dish_id', $dishId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? $result['dishes_name'] : 'Ismeretlen termék';
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


    public function getDishesImage(int|null $dishesImage)
    {
        if (empty($dishesImage)) {
            return '';
        }

        $result = $this->readMeta(['*'], ['dishes_image' => $dishesImage], 'dishes');

        /*if (empty($result) || !array_key_exists('meta_value', $result)) {
            return '';
        }*/

        return $result['dishes_image'];
    }

    public function getDishesById()
    {

        $result = $this->readMeta(['*'], [], 'dishes');

        return $result;
    }

    public function getDishesBySlug($dishesSlug)
    {

        $result = $this->readMeta(['*'], ['slug' => $dishesSlug], 'dishes');

        return $result;
    }

    public function getDishesImageBySlug($dishesSlug)
    {

        $result = $this->readMeta(['dishes_image'], ['slug' => $dishesSlug], 'dishes');

        return $result;
    }

    public function deleteDishes($dishesId) {
        $conditions = [
            'id' => $dishesId
        ];
        return $this -> delete($conditions);
    }
}
