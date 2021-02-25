<?php
class core_view_system extends core_singleton
{
    private $list = [];

    final public function set($param, $val)
    {
        do {
            if (empty($param) || !is_string($param)) break;
            if(!is_array($this->list)) $this->list = [];
            $this->list[$param] = $val;
        } while(false);
    }

    final public function get($param)
    {
        return !empty($param) && isset($this->list[$param]) ? $this->list[$param] : null;
    }
}
?>