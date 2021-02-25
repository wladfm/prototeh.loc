<?php
class core_query_dto implements core_query_idto
{
    public $error = null;
    public $data = null;

    public function get_error()
    {
        return $this->error;
    }

    public function set_error($err)
    {
        $this->error = $err;
    }

    public function get_data()
    {
        return $this->data;
    }

    public function set_data($value)
    {
        $this->data = $value;
    }
}
?>