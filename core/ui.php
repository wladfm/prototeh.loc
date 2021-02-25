<?php
class core_ui
{
    public $m = null;
    public $module_name = null;
    public entity_users $user;
    private array $errors;
    public entity_menu $menu;

    const SHORT_SESSION = 8;
    const LONG_SESSION = 720;

    public function __construct($query = false)
    {
        $is_auth = $this->access();

        $this->m = App::get($_GET, 'm') ?? 'main';
        $this->module_name = $this->m;
        if(!$query) {
            if ($is_auth && $this->m == 'logout') {
                entity_sessions::resetToken($_COOKIE['auth']);
                static::_redirect();
            }
            $this->menu = entity_menu::unique('m', $this->m);
            if (!$this->menu->get_init()) static::_redirect404();

            if ($this->m == 'login' && pages_login::auth()) {
                static::_redirect();
            }

            if ($this->menu->type_page == entity_menu::TYPE_PAGE_AUTH) {
                if (!$is_auth) {
                    static::_redirect('m=login');
                }
            } else {
                if ($is_auth) {
                    static::_redirect('m=list');
                }
            }
        } else {
            if(!$is_auth) {
                throw new QueryException('Пользователь не прошел аутентификацию');
            }
        }
        core_view_system::getInstance()->set('APPUI', $this);
    }

    // Отображение сайта
    public static function view()
    {
        $AppUI = new static();
        $class = 'pages_' . $AppUI->module_name;
        if(!class_exists($class)) static::_redirect404();

        try {
            core_view_start::view();
            $class::view();
            core_view_end::view();
        } catch (Throwable $tr) {
            core_log::set('ui', 'Файл: ' . $tr->getFile() . '. ' . $tr->getCode() . ": " . $tr->getMessage() . ". Строка: " . $tr->getLine());
        } catch (Exception $ex) {
            core_log::set('ui', 'Файл: ' . $ex->getFile() . '. ' . $ex->getCode() . ": " . $ex->getMessage() . ". Строка: " . $ex->getLine());
        }
    }

    // Редирект на заданную страницу
    public static function _redirect($url = '') {
        if (headers_sent()) {
            die('</head><body><script type="text/javascript">window.location.href = "/index.php?' . $url . '"</script></body>');
        } else {
            header('Location: /index.php?' . $url, true, 303);
            exit;
        }
    }

    // Редирект на 403
    public static function _redirect403() {
        session_destroy();
        if (headers_sent()) {
            die('</head><body><script type="text/javascript">window.location.href = "/403.html"</script></body>');
        } else {
            $msg = "ВЗЛОМ! IP - " . $_SERVER['REMOTE_ADDR'];
            core_log::set('auth', $msg);
            header('Location: /403.html', true, 403);
            exit;
        }
    }

    // Редирект на 404
    public static function _redirect404() {
        session_destroy();
        if (headers_sent()) {
            die('</head><body><script type="text/javascript">window.location.href = "/404.html"</script></body>');
        } else {
            header('Location: /404.html', true, 404);
            exit;
        }
    }

    // Установка глобальных ошибок
    public function set_error($err)
    {
        if(empty($err) || !is_string($err)) return;
        if(empty($this->errors)) $this->errors = [];
        $this->errors[] = $err;
    }

    // Возврат строки со всеми ошибками
    public function get_error()
    {
        if(empty($this->errors) || !is_array($this->errors) || count($this->errors) == 0) return null;
        $err = '';
        foreach ($this->errors as $error) {
            if(!is_string($error) || empty($error)) continue;
            $err .= (!empty($err) ? PHP_EOL : '') . $error;
        }
        return $err;
    }

    /**
     * Проверка доступа
     * @return bool
     */
    private function access()
    {
        $res = false;

        do {
            $auth = $_COOKIE['auth'];
            if(empty($auth)) break;
            $id_user = null;
            // Проверяем токен
            if(!entity_sessions::check($auth, $id_user)) break;
            // Проверяем пользователя
            $user = entity_users::id($id_user);
            if(!$user->get_init()) break;
            $this->user = $user;

            $res = true;
        } while(false);

        return $res;
    }

    // Выполнение запроса
    public static function query()
    {
        try {
            try {
                $AppUI = new static(true);
                $class = 'requests_' . $AppUI->module_name;
                $query_object = new core_query_dto();

                if(!class_exists($class)) throw new QueryException('Не найден класс ' . $class);
                $object = new $class();
                $object->request();
                $query_object = $object->get_dto();
                unset($object);
            } catch (QueryException $qex) {
                core_log::set('requests', 'Файл: ' . $qex->getFile() . '. ' . $qex->getCode() . ": " . $qex->getMessage() . ". Строка: " . $qex->getLine());
                $query_object->set_error('Ошибка выполнения запроса. Обратитесь к системному администратору');
            }
        } catch (Throwable $tr) {
            core_log::set('requests', 'Файл: ' . $tr->getFile() . '. ' . $tr->getCode() . ": " . $tr->getMessage() . ". Строка: " . $tr->getLine());
        } catch (Exception $ex) {
            core_log::set('requests', 'Файл: ' . $ex->getFile() . '. ' . $ex->getCode() . ": " . $ex->getMessage() . ". Строка: " . $ex->getLine());
        }

        return $query_object;
    }
}
?>