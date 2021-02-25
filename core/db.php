<?php
class core_db extends core_object
{
    protected $_tbl = null;
    protected $_key = null;
    private $pdo;

    public function __construct($table = '', $key = '')
    {
        $this->_tbl = $table;
        $this->_key = $key;
        if(!empty($key))
            $this->$key = null;
    }

    protected function init()
    {
        $class = new static();
        $fields = get_class_vars(get_class($class));
        $not_field = ['pdo', '_tbl', '_key'];
        foreach ($fields as $k => $v) {
            if(in_array($k, $not_field)) continue;
            if(is_string($this->$k)) $this->$k = '';
            else if(is_numeric($this->$k)) $this->$k = 0;
            else if(is_object($this->$k)) $this->$k = null;
            else if(is_array($this->$k)) $this->$k = [];
            else $this->$k = null;
        }
        unset($class);
    }

    // создание соединения
    private function connect()
    {
        $dsn = "mysql:host=" . includes_config::HOST_DB . ";dbname=" . includes_config::NAME_DB . ";charset=" . includes_config::CHARSET;
        $connopt = array(
            PDO::ATTR_ERRMODE  => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
        $this->pdo = new PDO($dsn, includes_config::USER_DB, includes_config::USER_DB_PASSWORD, $connopt);
    }

    // проверка соединения
    private function get_status()
    {
        if(is_null($this->pdo)) {
            return false;
        } elseif ($this->pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS) === includes_config::HOST_DB . " via TCP/IP") {
            return true;
        } else {
            return false;
        }
    }

    // деструктор соединения
    private function destroy()
    {
        $this->pdo = null;
    }

    // выполнение запроса
    private function execute($query, $placeholders = null, $select = true)
    {
        $this->connect();
        if($this->get_status()) {
            //отключаем эмуляцию
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare($query);
            if(!is_null($placeholders)) {
                $stmt->execute($placeholders);
            } else {
                $stmt->execute();
            }
            if ($select) {
                $arr = $stmt->fetchAll(PDO::FETCH_PROPS_LATE);
                $this->pdo->commit();
                $this->destroy();
                return $arr;
            }
            else {
                $id = $this->pdo->lastInsertId();
                $this->pdo->commit();
                $this->destroy();
                if($id == 0)
                    return true;
                return $id;
            }
        } else {
            $this->pdo->commit();
            $this->destroy();
            return false;
        }
    }

    // глобальное выполнение запроса
    public function query($query, $placeholders = null, $select = true)
    {
        return $this->execute($query, $placeholders, $select);
    }

    // загрузка по ИД записи
    public function load($id = null)
    {
        $msg = null;

        do {
            $key_id = $this->_key;
            if(!empty($id)) {
                $this->$key_id = $id;
            }
            if(empty($this->$key_id)) {
                $msg = get_class($this) . ': Не задан идентификатор';
                break;
            }

            $sql = 'SELECT * FROM ' . $this->_tbl . ' WHERE ' . $this->_key . '=:' . $this->_key;
            $w = array($this->_key => $this->$key_id);
            $request = $this->query($sql, $w, true);
            if($request === false) {
                $msg = get_class($this) . ': Не удалось создать соединение';
                break;
            }
            if(empty($request)) {
                $msg = get_class($this) . ': Запись не найдена';
                break;
            }
            $this->bind($request[0]);

        } while(false);

        if($msg !== null) core_log::set('db', $msg);

        return $msg;
    }

    // запись или обновление
    public function store() {
        $msg = null;

        do {
            $k = $this->_key;
            if(empty($this->$k)) {
                $msg = $this->insert();
            } else {
                $msg = $this->update();
            }
        } while(false);

        if($msg !== null) core_log::set('db', $msg);

        return $msg;
    }

    // новая запись
    private function insert()
    {
        $msg = null;
        $not_field = ['pdo', '_tbl', '_key', $this->_key];
        do {
            $field = '';
            $val = '';
            $w = array();
            foreach ($this as $k => $v) {
                if(!in_array($k, $not_field) && !empty($v)) {
                    $field .= $k . ',';
                    $val .= ':' . $k . ',';
                    $w[$k] = $v;
                }
            }
            if(empty($val)) {
                $msg = get_class($this) . ': Нет полей для записи';
                break;
            }
            $field = trim($field, ',');
            $val = trim($val, ',');
            $sql = 'INSERT INTO ' . $this->_tbl . ' (' . $field . ') VALUES (' . $val . ')';
            $request = $this->query($sql, $w, false);
            if(empty($request)) {
                $msg = get_class($this) . ': Не удалось записать запись';
                break;
            }
            $msg = $this->load($request);
        } while(false);

        return $msg;
    }

    // обновление
    private function update() {
        $msg = null;
        $not_field = ['pdo', '_tbl', '_key', $this->_key];
        do {
            $w = array();
            $val = '';
            foreach ($this as $k => $v) {
                if(!in_array($k, $not_field) && $v !== null) {
                    $val .= $k . '=:' . $k . ',';
                    $w[$k] = $v;
                }
            }
            if(empty($val)) {
                $msg = get_class($this) . ': Нет полей для обновления';
                break;
            }
            $val = trim($val, ',');
            $key = $this->_key;
            $w[$key] = $this->$key;
            $sql = 'UPDATE ' . $this->_tbl . ' SET ' . $val . ' WHERE ' . $key . '=:' . $key;
            $request = $this->query($sql, $w, false);
            if(empty($request)) {
                $msg = get_class($this) . ': Не удалось обновить запись';
                break;
            }
            $msg = $this->load($this->get_id());
        } while(false);

        return $msg;
    }

    // удаление
    public function delete() {
        $msg = null;

        do {
            $id = $this->_key;
            if(empty($this->_key) || empty($this->$id)) {
                $msg = get_class($this) . ': Объект не инициализирован';
                break;
            }

            $sql = 'DELETE FROM ' . $this->_tbl . ' WHERE ' . $this->_key . '=:id';
            $w = array(
                'id' => $this->$id
            );
            $request = $this->query($sql, $w, false);
            if(empty($request)) {
                $msg = get_class($this) . ': Не удалось удалить запись';
                break;
            }
            // Удаляем объект
            $this->__destruct();
        } while(false);

        if($msg !== null) core_log::set('db', $msg);

        return $msg;
    }

    // произвольный запрос
    public static function select($query, $placeholders = null, $select = true) {
        $class = new static();
        return $class->query($query, $placeholders, $select);
    }

    /**
     * создание класса и загрузка
     * @param int $id
     * @return static
     */
    public static function id($id)
    {
        $class = new static();
        if(null !== $class->load($id)) $class = new static();
        return $class;
    }

    // сброс значения ключа (для копирования данных)
    public function reset() {
        $k = $this->_key;
        $this->$k = null;
    }

    // очистка объекта
    public function __destruct()
    {
        if(!$this->get_init()) return;
        $this->init();
    }

    /**
     * загрузка по уникальному полю
     * @param string $field - наименование поля
     * @param string $value - значение
     * @return static
     */
    public static function unique($field, $value) {
        $class = new static();

        do {
            if(empty($field) || !$class->checkField($field)) {
                break;
            }
            $sql = 'SELECT ' . $class->get('_key') . ' FROM ' . $class->get('_tbl') . ' WHERE ' . $field . '=:' . $field . ' ORDER BY ' . $class->get('_key') . ' DESC LIMIT 1';
            $w = array($field => $value);
            $request = $class->query($sql, $w, true);
            if($request === false) {
                break;
            }
            if(empty($request)) {
                break;
            }
            $class = static::id($request[0][$class->get('_key')]);
        } while(false);

        return $class;
    }

    /**
     * загрузка полного списка
     * @param array $where имеет два поля: sql - запрос вида "key1=:key1,key2=:key2,..."; param - объект со значениями ключей
     * @param string $field - поле по которому сортировать (по умолчанию ИД)
     * @param int $desk - тип сортировки (0 - по возрастанию, 1 - по убыванию)
     * @param int $limit - количество выбираемых полей
     * @return array::static
     */
    public static function all($where = null, $field = null, $desk = 0, $limit = null)
    {
        $list = array();
        $msg = null;
        $class = new static();
        do {
            $_WHERE = '';
            if(!empty($where) && is_array($where) && isset($where['sql'])) {
                $_WHERE = ' WHERE ' . $where['sql'];
                if(isset($where['param']))
                    $w = $where['param'];
            }
            if (empty($field) || !property_exists($class, $field))
                $field = $class->get('_key');
            $_DESC = ' ASC';
            if ($desk != 0) {
                $_DESC = ' DESC';
            }
            $_LIMIT = '';
            if (!empty($limit)) {
                $_LIMIT = ' LIMIT ' . intval($limit);
            }
            $sql = 'SELECT * FROM ' . $class->get('_tbl') . $_WHERE . ' ORDER BY ' . $field . $_DESC . $_LIMIT;
            $request = $class->query($sql, $w, true);
            if($request === false) {
                $msg = get_class($class) . ': Не удалось создать соединение';
                break;
            }
            foreach ($request as $k => $v) {
                $e = new static();
                if(null !== ($msg = $e->bind($v))) {
                    $list = array();
                    unset($e);
                    break;
                }
                $list[] = $e;
                unset($e);
            }
        } while(false);

        if($msg !== null) core_log::set('db', $msg);
        unset($class);

        return $list;
    }

    /**
     * Возврат ИД объекта
     * @return int - ИД объекта
     */
    public function get_id()
    {
        $key = $this->get('_key');
        return $this->$key;
    }

    /**
     * Проверка инициализации объекта
     * @return bool
     */
    public function get_init()
    {
        return !empty($this->get_id());
    }

    /**
     * Произвольный запрос (результат массив текущих объектов)
     * @param $query
     * @param null $placeholders
     * @param bool $select
     * @return array
     */
    public static function list($query, $placeholders = null, $select = true)
    {
        $request = static::select($query, $placeholders, $select);
        $list = [];
        foreach ($request as $k => $v) {
            $e = new static();
            if(null !== ($msg = $e->bind($v))) {
                $list = array();
                unset($e);
                break;
            }
            $list[] = $e;
            unset($e);
        }

        return $list;
    }
}
?>