<?php
class pages_main implements core_view_body
{
    public static function view()
    {
        echo '<h1>Добро пожаловать!</h1>';
        echo '<h3>Для продолжения работы, выполните вход в систему. Если Вы незарегистрированы, то пройдите регистрацию</h3>';
    }
}
?>