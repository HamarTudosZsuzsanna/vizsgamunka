<?php
require_once(CLASSES_DIR . '/FormController.php');

class UserFormController extends FormController
{
    public static function save(array $data, array $definitions)
    {
        $errors = parent::validate($data, $definitions);

        if (!empty($errors)) {
            return $errors;
        }

        if ($errors === false) {
            return;
        }

        if (array_key_exists('password_repeat', $data)) {
            unset($data['password_repeat']);
        }

        $data['accepted'] = isset($data['accepted']) ? 1 : 0;

        $userData = requestFilter(['email', 'first_name', 'last_name', 'password'], $data);
        $user = new User();
        $userId = $user->create($userData);
        $userMetaData = requestFilter(['address', 'phone', 'number', 'accepted'], $data);
        $user->createMeta($userMetaData, $userId);
        redirect('/profile');
    }

    public static function update(array $data, array $definitions)
    {
        $errors = parent::validate($data, $definitions);

        if (!empty($errors)) {
            return $errors;
        }

        if ($errors === false) {
            return;
        }

        if (array_key_exists('password_repeat', $data)) {
            unset($data['password_repeat']);
        }

        
        $userData = requestFilter(['id', 'email', 'first_name', 'last_name', 'password'], $data);
        $user = new User();
        $result = $user->update($userData, ['id' => $userData['id']]);

        if (!$result) {
            return false;
        }


        $updatedData = $user->getByEmail($userData['email'], ['*']);
        $_SESSION['logged_in'] = $updatedData;

        if (!empty($userMetaData = requestFilter(['address', 'phone', 'number'], $data))) {
            $user->createMeta($userMetaData, $userData['id']);
        }

        redirect('/profile/update');
    }


    public static function login($data, $definitions)
    {
        $errors = parent::validate($data, $definitions);

        if (!empty($errors)) {
            return $errors;
        }

        if ($errors === false) {
            return;
        }

        $user = new User();
        $user = $user->getByEmail($data['email'], ['*']);

        if (!empty($user)) {
            $_SESSION['logged_in'] = $user;
        }
    }


}