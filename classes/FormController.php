<?php
require(MODELS_DIR . '/User.php');

class FormController {
    public static string $definitionName = '';
    public static array $formData = [];

    public static function getDefinition(string $name): array|null {
        $path = sprintf('%s/%sFormDefinition.json', DEFINITIONS_DIR, $name);
        self::$definitionName = $name;
        return json_decode(file_get_contents($path), true);
    }
    
    public static function getFieldValue(string|int $key): string|int {
        if (empty(self::$formData) && !array_key_exists($key, self::$formData)) {
            return '';
        }
    
        return self::$formData[$key];
    }
    
    public static function validate(array $data, array|null $definitions): array|false {
        if (!empty($data)) {
            self::$formData = $data;
        }
        
        if (empty(self::$formData)) {
            return false;
        }
        
        foreach (self::$formData as &$fieldData) {
            $fieldData = self::sanitizeData($fieldData);
        }
        
        $errors = self::validateFormData($definitions);
        return $errors;
    }

    private static function validateFormData(array|null $definitions): array {
        $errors = [];

        try {
            if ($definitions === null) {
                throw new Exception('Json cannot be decoded!');
            }

            foreach (self::$formData as $key => $fieldData) {
                foreach ($definitions as $definition) {
                    if ($definition['key'] === $key && array_key_exists('rules', $definition)) {
                        foreach ($definition['rules'] as $rule) {
                            $isError = false;

                            switch($rule['type']) {
                                case 'regex':
                                    if (!preg_match($rule['condition'], $fieldData)) {
                                        $isError = true;
                                    }
                                break;
                                case 'min':
                                    if (strlen($fieldData) < $rule['condition']) {
                                        $isError = true;
                                    }
                                break;
                                case 'max':
                                    if (strlen($fieldData) > $rule['condition']) {
                                        $isError = true;
                                    }
                                break;
                                case 'compare_equal':
                                    if ($fieldData !== self::$formData[$rule['condition']]) {
                                        $isError = true;
                                    }
                                break;
                                case 'check_email':
                                    if ($rule['field'] === 'email') {
                                        $user = new User();
                                        
                                        if ($rule['condition'] === 'equal' && !empty($user->getByEmail($fieldData))) {
                                            $isError = true;
                                        } else if ($rule['condition'] === 'not_equal' && empty($user->getByEmail($fieldData))) {
                                            $isError = true;
                                        }
                                    }
                                break;
                                case 'verify_password':
                                    if ($rule['field'] === 'password') {
                                        $user = new User();
                                        if (!$user->verifyPassword(self::$formData['email'], self::$formData['password'])) {
                                            $isError = true;
                                        }
                                    }
                                break;
                            }

                            if ($isError) {
                                if (array_key_exists('error', $rule)) {
                                    $errors[$key] = $rule['error'];
                                    continue;
                                }
                                $errors[$key] = 'Helytelenül kitöltött mező';
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            echo "Váratlan hiba történt! <a href='/'>Vissza a főoldalra</>";
            error_log($e);
            exit;
        }
    
        return $errors;
    }

    public static function sanitizeData(string|int|array $data) {
        if (!is_string($data)) {
            return $data;
        }

        return strip_tags($data);
    }
}