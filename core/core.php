<?php
// Загрузчик классов
spl_autoload_register(function($class)
{
    $dir = $_SERVER['DOCUMENT_ROOT'];
    $classname = str_replace('_', '/', $class);
    $filename = $dir . "/" . $classname . ".php";
    if(file_exists($filename))
        include_once($filename);
    else
        core_log::set('system', 'Не найден класс ' . $class);
});

class QueryException extends Exception {}

class App
{
    //вывод текущей даты
    public static function CurrentDate($format = includes_variable::DB_DATE_FORMAT)
    {
        return date($format);
    }

    //вывод даты в формате
    public static function FormatDate($date = '', $format = Constants::DB_DATE_FORMAT)
    {
        $dt = $date;

        do {
            if (empty($date))
                break;
            if (($timestamp = strtotime($date)) === false) {
                break;
            }
            $dt = date($format, $timestamp);
        } while (false);

        return $dt;
    }

    // Добавление часов к дате
    public static function addHour($date, $hour, $format = Constants::DB_DATETIME_FORMAT)
    {
        $dt = $date;

        do {
            if (empty($date) || !is_numeric($hour))
                break;
            $dt = self::FormatDate($date . ($hour >= 0 ? '+ ' : '- ') . abs($hour) . ' hour', $format);
        } while (false);

        return $dt;
    }

    // Сравнение дат
    public static function CompareDate($date1, $date2)
    {
        $result = false;

        do {
            if (($timestamp1 = strtotime($date1)) === false) break;
            if (($timestamp2 = strtotime($date2)) === false) break;

            if($timestamp1 < $timestamp2) $result = -1;
            else if($timestamp1 > $timestamp2) $result = 1;
            else $result = 0;
        } while(false);

        return $result;
    }

    // Возвращает параметр из объекта/массива, если он есть
    public static function get($object, $param)
    {
        $result = null;

        do {
            if(is_null($object) || empty($param)) break;

            if(!is_object($object) && !is_array($object)) break;

            if(is_object($object)) {
                $result = isset($object->$param) ? $object->$param : null;
            } else if(is_array($object)) {
                $result = isset($object[$param]) ? $object[$param] : null;
            }
        } while(false);

        return $result;
    }
}

?>