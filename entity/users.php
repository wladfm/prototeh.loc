<?php
class entity_users extends core_db
{
    // Логин
    public string $login;
    // Пароль (хэш)
    public string $pass;
    // Имя
    public string $name;

    const PASSWORD_LENGTH = 7;

    public function __construct($table = '', $key = '')
    {
        parent::__construct('users', 'ID');
    }

    /**
     * Проверка пользователя
     * @return bool
     */
    public function valid()
    {
        $res = false;

        do {
            // Ищем по логину
            $user = static::unique('login', $this->login);
            // Если нет такого
            if(!$user->get_init()) break;
            // Проверяем пароль
            $ver = password_verify($this->pass, $user->pass);
            if(!$ver) break;
            $res = true;
        } while(false);

        return $res;
    }

    /**
     * Создание хэша пароля
     * @param $pass
     */
    private function pass_hash(&$pass)
    {
        $pass = password_hash($pass, PASSWORD_DEFAULT);
    }

    /**
     * Сохранение
     * @return string|null
     */
    public function store()
    {
        $msg = null;

        do {
            $user = static::unique('login', $this->login);
            if($user->get_init()) {
                $msg = 'Пользователь с таким логином существует';
                break;
            }
            if(null !== ($msg = $this->valid_password($this->pass))) break;
            $this->pass_hash($this->pass);
            if(null !== ($msg = parent::store())) break;
        } while(false);

        return $msg;
    }

    // Валидация нового пароля
    private function valid_password($pass)
    {
        $msg = null;

        do {
            if(!is_string($pass) || strlen($pass) < static::PASSWORD_LENGTH) {
                $msg = 'Длина пароля должна быть не меньше ' . static::PASSWORD_LENGTH . ' символов';
                break;
            }
            if(!preg_match("/(([a-zA-Z]\d)|(\d[a-zA-Z]))+/", $pass)) {
                $msg = 'Символы в пароле должны быть только буквами латинского алфавита и цифрами';
                break;
            }
        } while(false);

        return $msg;
    }
}
?>