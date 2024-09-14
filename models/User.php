<?php
require_once('Model.php');
require(TRAITS_DIR . '/MetaData.php');

class User extends Model
{
    use MetaData;

    protected string $tableName = 'users';

    protected array $fillables = [
        'email',
        'first_name',
        'last_name',
        'password',
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


    public function getByEmail(string $email, array $columns = ['id'])
    {
        return $this->filterGuarded($this->read($columns, ['email' => $email]));
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


   /* public function getProfileImage(int|null $userId)
    {
        if (empty($userId)) {
            return '';
        }

        $result = $this->readMeta(['*'], ['user_id' => $userId, 'meta_key' => 'profile_img'], 'users_meta');

        if (empty($result) || !array_key_exists('meta_value', $result)) {
            return '';
        }

        return $result['meta_value'];
    }
        */


}