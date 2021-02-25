<?php
abstract class core_query_manager
{
    protected core_query_dto $dto;
    protected core_object $post;
    protected core_object $get;

    public function __construct()
    {
        $this->dto = new core_query_dto();
        // обрабатываем GET
        $this->get = new core_object();
        foreach ($_GET as $g => $g_val) {
            if(isset($this->get->$g)) continue;
            $this->get->$g = $g_val;
        }
        // обрабатываем POST
        $this->post = new core_object();
        foreach ($_POST as $p => $p_val) {
            if(isset($this->post->$p)) continue;
            $this->post->$p = $p_val;
        }
    }

    /**
     * Устанавливаем ошибку
     * @param $err - Строка ошибки
     */
    public function set_error($err)
    {
        $this->dto->set_error($err);
    }

    /**
     * Устанавливаем возвращаемые значения
     * @param $value - объект ответа
     */
    public function set_data($value)
    {
        $this->dto->set_data($value);
    }

    /**
     * Возврат переменной запроса
     * @param null $param - имя параметра
     * @return null|object|array - сохраненное значение
     */
    protected function get($param = null)
    {
        $result = null;

        do {
            if(empty($param) || !is_string($param)) break;
            if(isset($this->post->$param) && !empty($this->post->$param)) {
                $result = $this->post->$param;
                break;
            }
            if(isset($this->get->$param) && !empty($this->get->$param)) {
                $result = $this->get->$param;
                break;
            }
        } while(false);

        return $result;
    }

    /**
     * Возврат объекта ответа
     * @return core_query_dto
     */
    public function get_dto()
    {
        return $this->dto;
    }

    /**
     * Выполнение запроса
     */
    public function request(){}
}
?>