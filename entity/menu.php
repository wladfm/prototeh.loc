<?php
class entity_menu extends core_db
{
    public $name = null; // Наименование
	public $m = null; // Имя модуля
    public $type_page = null; // Тип страницы

    // Типы страниц
    const TYPE_PAGE_NO_AUTH = 0; // Страницы непрошедших авторизацию
    const TYPE_PAGE_LOGIN = 1; // Страница входа или регистрации
    const TYPE_PAGE_AUTH = 2; // Страницы прошедших авторизацию

    public function __construct()
    {
        parent::__construct('menu', 'ID');
    }

    public static function getName()
    {
        $res = '';

        do {
            $AppUI = core_view_system::getInstance()->get('APPUI');
            if(empty($AppUI)) break;
            $menu = static::unique('m', $AppUI->m);
            if(!$menu->get_init()) break;
            $res = $menu->name;
        } while(false);

        return $res;
    }
}
?>