<?php
class core_view_start
{
    public static function view()
    {
        static::_document();
        static::page();
    }

    protected static function page()
    {
        $AppUI = core_view_system::getInstance()->get('APPUI');

        if($AppUI->menu->type_page !== entity_menu::TYPE_PAGE_LOGIN) {
            $nav = '';
            if($AppUI->menu->type_page == entity_menu::TYPE_PAGE_AUTH) {
                $list = entity_menu::all(['sql' => 'type_page=' . entity_menu::TYPE_PAGE_AUTH]);
                foreach ($list as $item) {
                    $nav .= '<li class="c-nav__item">
                                <a class="c-nav__link" href="/index.php?m=' . $item->m . '">' . $item->name . '</a>
                            </li>';
                }
            } else {
                $nav = '<li class="c-nav__item">
                        <a class="c-nav__link" href="/index.php?m=login">Вход</a>
                    </li>
                    <li class="c-nav__item">
                        <a class="c-nav__link" href="/index.php?m=registration">Регистрация</a>
                    </li>';
            }

            echo '<header class="c-navbar u-mb-medium">
            <nav class="c-nav collapse" id="main-nav">
                <ul class="c-nav__list">
                    ' . $nav . '
                </ul>
            </nav>
            ' . ($AppUI->menu->type_page == entity_menu::TYPE_PAGE_AUTH ? static::getUser() : '') . '
        </header>';
            echo '<main class="o-page__content">';
        }

    }

    protected static function _document()
    {
        echo '<!doctype html>';
        echo '<html lang="ru">';
        echo '<head>';
        echo '<meta charset="utf-8">';
        echo '<title>' . entity_menu::getName() . '</title>';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '<link href="/core/view/css/main.min.css?v=' . includes_config::VERSION_APP . '" rel="stylesheet">';
        echo '<link href="/core/view/css/view.css?v=' . includes_config::VERSION_APP . '" rel="stylesheet">';
        echo '<link href="/core/view/css/fonts.min.css?v=' . includes_config::VERSION_APP . '" rel="stylesheet">';
        // Загружаем пользовательские стили
        $file = $_SERVER['DOCUMENT_ROOT'] . '/pages/css/' . core_view_system::getInstance()->get('APPUI')->module_name . '.css';
        if(file_exists($file)) {
            echo '<link href="/pages/css/' . core_view_system::getInstance()->get('APPUI')->module_name . '.css?v=' . includes_config::VERSION_APP . '" rel="stylesheet">';
        }
        echo '</head>';
        echo '<body>';
    }

    private static function getUser()
    {
        $AppUI = core_view_system::getInstance()->get('APPUI');

        return '<div class="c-dropdown dropdown" style="margin-left:auto">
                <a  class="c-avatar c-avatar--xsmall has-dropdown dropdown-toggle" href="#" id="dropdwonMenuAvatar" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    ' . $AppUI->user->name . '
                </a>

                <div class="c-dropdown__menu dropdown-menu dropdown-menu-right" aria-labelledby="dropdwonMenuAvatar">
                    <a class="c-dropdown__item dropdown-item" href="/index.php?m=logout">Выход</a>
                </div>
            </div>';
    }
}
?>