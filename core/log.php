<?php
class core_log
{
    public $filename = null;

    public function __construct($filename)
    {
        if(empty($filename) || !is_string($filename))
            return;
        $this->filename = $_SERVER['DOCUMENT_ROOT'] . '/logs/' . $filename . '.log';
    }

    public function store($msg = null, $byDate = true) {
        do {
            if(empty($this->filename) || !is_string($this->filename))
                return;
            if(empty($msg) || !is_string($msg))
                break;
            $msg = ($byDate ? App::CurrentDate(includes_variable::DB_DATETIME_FORMAT) . "\t" : '') . $msg . "\r\n";
            if(!file_exists($this->filename)) {
                $fp = fopen($this->filename, "w");
            } else {
                $fp = fopen($this->filename, "a");
            }
            fwrite($fp, $msg);
            fclose($fp);
        } while(false);
    }

    public static function set($filename, $msg, $byDate = true)
    {
        $log = new static($filename);
        $log->store($msg, $byDate);
    }
}
?>