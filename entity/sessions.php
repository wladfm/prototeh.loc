<?php
class entity_sessions extends core_db
{
    public $id_user = null; // ИД пользователя
	public $ip = null; // IP-адрес клиента
	public $user_agent = null; // Клиент
	public $token = null; // Токен
	public $date_start = null; // Дата регистрации токена
	public $date_end = null; // Дата действия токена

    public function __construct()
    {
        parent::__construct('sessions', 'ID');
    }

    // Инициализация токена
    private function initToken()
    {
        $msg = null;

        do {
            $i = 0;

            while(true) {
                $str_token = bin2hex(random_bytes(255));
                $session = static::unique('token', $str_token);
                if(!$session->get_init()) {
                    $this->token = $str_token;
                    break;
                }
                //Если за 7 не смогли создать уникальный токен, то выдаем ошибку
                if($i >= 7) {
                    $msg = "Не удалось создать токен";
                    break(2);
                }
                $i++;
            }
        } while(false);

        if(isset($session)) unset($session);

        return $msg;
    }

    // Сохранение сессии
    public function store()
    {
        $msg = null;

        do {
            // Для текущего пользователя удаляем предыдущие токены
            if(null !== ($msg = $this->clear())) break;
            // Инициализируем токен
            if(null !== ($msg = $this->initToken())) break;
            $msg = parent::store();
        } while(false);

        return $msg;
    }

    // Удаление всех токенов пользователя
    public function clear()
    {
        $msg = null;

        do {
            $list = static::all(['sql' => 'id_user=:us', 'param' => ['us' => $this->id_user]]);
            foreach ($list as $s) {
                if(!is_a($s, get_class($this))) continue;
                if(null !== ($msg = $s->delete())) break(2);
            }
        } while(false);

        return $msg;
    }

    // Проверка токена
    public static function check($key, &$id_user)
    {
        $check = false;

        do {
            // Ищем токен
            $session = static::unique('token', $key);
            if(!$session->get_init()) break;
            // Проверяем параметры
            if($session->ip != $_SERVER['REMOTE_ADDR']) break;
            if($session->user_agent != $_SERVER['HTTP_USER_AGENT']) break;
            // Проверяем актуальность токена
            if(App::CompareDate(App::CurrentDate(includes_variable::DB_DATETIME_FORMAT), $session->date_start) === -1) break;
            if(App::CompareDate(App::CurrentDate(includes_variable::DB_DATETIME_FORMAT), $session->date_end) === 1) break;
            $check = true;
            $id_user = $session->id_user;
        } while(false);

        if(!$check && isset($session)) $session->clear();

        if(isset($session)) unset($session);

        return $check;
    }

    // Сброс токена
    public static function resetToken($key)
    {
        $result = true;

        do {
            // Ищем токен
            if(empty($key)) break;
            $session = static::unique('token', $key);
            if(!$session->get_init()) break;
            if(null !== $session->clear()) {
                $result = false;
                break;
            }
        } while(false);

        if(isset($session)) unset($session);

        return $result;
    }
}
?>