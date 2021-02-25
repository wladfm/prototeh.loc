<?php
class core_view_end extends core_view_start
{
    protected static function page()
    {
        echo '</main>';
    }

    protected static function _document()
    {
        echo '<script src="/core/view/scripts/main.min.js?v=' . includes_config::VERSION_APP . '"></script>';
        echo '<script src="/core/view/scripts/view.js?v=' . includes_config::VERSION_APP . '"></script>';
        // Пользовательские скрипты
        $file = $_SERVER['DOCUMENT_ROOT'] . '/pages/scripts/' . core_view_system::getInstance()->get('APPUI')->module_name . '.js';
        if(file_exists($file)) {
            echo '<script src="/pages/scripts/' . core_view_system::getInstance()->get('APPUI')->module_name . '.js?v=' . includes_config::VERSION_APP . '"></script>';
        }
        echo '</body>';
        echo '</html>';
    }
}
?>