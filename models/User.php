<?php
require_once('Model.php');
require(TRAITS_DIR . '/MetaData.php');

class User extends Model
{
    use MetaData;

    protected string $tableName = 'users';

    protected array $fillables = [
        'id',
        'email',
        'first_name',
        'last_name',
        'password',
    ];

    protected array $fillablesData = [
        'phone',
        'address',
        'number',
    ];

    protected array $casts = [
        'password' => 'encrypt'
    ];

    protected array $guarded = [
        'password'
    ];


    public function create(array $data): string|false
    {
        $data = parent::castValues($data);
        return parent::create($data, $this->tableName);
    }

    public function update(array $data, array $conditions): string|false
    {
        $data = parent::castValues($data);
        return parent::update($data, $conditions);
    }

    public function updateUserData(array $data, string $userId): bool
    {

        $queryString = "UPDATE users_meta SET phone = :phone, address = :address, number = :number WHERE user_id = :user_id";

        $params = [
            ':phone' => $data['phone'],
            ':address' => $data['address'],
            ':number' => $data['number'],
            ':user_id' => $userId
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


    public function getByEmail(string $email, array $columns = ['id'])
    {
        return $this->filterGuarded($this->read($columns, ['email' => $email]));
    }

    public function getByUserId($userId)
    {
        return $this->readMeta(['*'], ['user_id' => $userId], 'users_meta');
    }

    public function getByUserOrder($userId)
    {
        return $this->readMeta(['*'], ['user_id' => $userId], 'orders');
    }

    public function getByOrderItem(array $orderIds)
    {
        
        if (empty($orderIds)) {
            return [];
        }
        // Az order_id-k beállítása az SQL lekérdezéshez
        $placeholders = rtrim(str_repeat('?,', count($orderIds)), ',');
        $queryString = "SELECT * FROM orders_item WHERE order_id IN ($placeholders)";

        $stmt = $this->connection->prepare($queryString);
        $stmt->execute($orderIds);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByUserOrderCart($userId)
    {
        return $this->readMeta(['*'], ['cart_user_id' => $userId], 'cart_item');
    }

    public function verifyPassword(string $email, string $password)
    {
        $user = $this->read(['password'], ['email' => $email]);
        return password_verify($password, $user['password']);
    }

    public function logout()
    {
        $_SESSION['logged_in'] = null;
    }

    private function filterGuarded(array $data)
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->guarded)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    public function filterFillables(array $data)
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $this->fillables)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    public function filterFillablesData(array $data)
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $this->fillablesData)) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
