<?php
class pages_login implements core_view_body
{
    public static function view()
    {
        $error = core_view_system::getInstance()->get('APPUI')->get_error();

        $msg_error = $error !== null ? '<div class="c-alert c-alert--danger alert fade show">
                        <i class="c-alert__icon fa fa-times-circle"></i> ' . $error . '
                        <button class="c-close" data-dismiss="alert" type="button">&times;</button>
                    </div>' : '';

        echo '
            <div class="o-page o-page--center o-page__card">
                <div class="c-card u-mb-xsmall">
                    <header class="c-card__header u-pt-large">
                        <h1 class="u-h3 u-text-center u-mb-zero">Добро пожаловать!</h1>
                    </header>
                    
                    ' . $msg_error . '
            
                    <form class="c-card__body" method="post">
                        <div class="c-field u-mb-small">
                            <label class="c-field__label" for="login">Логин</label>
                            <input class="c-input" type="username" id="login" name="login" placeholder="Введите логин">
                        </div>
            
                        <div class="c-field u-mb-small">
                            <label class="c-field__label" for="pass">Пароль</label>
                            <input class="c-input" type="password" id="pass" name="pass" placeholder="Введите пароль">
                        </div>
            
                        <div class="c-choice c-choice--checkbox">
                            <input class="c-choice__input" id="save_me" name="save_me" type="checkbox">
                            <label class="c-choice__label" for="save_me">Оставаться в системе</label>
                        </div>
            
                        <button class="c-btn c-btn--info c-btn--fullwidth" type="submit">Войти</button>
                        
                        <a href="/index.php?m=registration">Регистрация</a>
                        
                    </form>
                </div>
            </div>';
    }

    public static function auth()
    {
        $res = false;

        do {
            $data = $_POST;
            if(!isset($data['login']) && !isset($data['pass'])) break;
            if(!isset($data['login']) || empty($data['login'])) {
                core_view_system::getInstance()->get('APPUI')->set_error('Введите логин');
                break;
            }
            if(!isset($data['pass']) || empty($data['pass'])) {
                core_view_system::getInstance()->get('APPUI')->set_error('Введите пароль');
                break;
            }
            $user = new entity_users();
            $user->login = $data['login'];
            $user->pass = $data['pass'];
            if(!$user->valid()) {
                core_view_system::getInstance()->get('APPUI')->set_error('Пользователь или пароль введены неверно');
                break;
            }
            $user = entity_users::unique('login', $user->login);
            if(!$user->get_init()) {
                core_view_system::getInstance()->get('APPUI')->set_error('Произошла ошибка. Обратитесь к систменому администратору');
                break;
            }
            // Создаем сессию
            $sessions = new entity_sessions();
            $sessions->id_user = $user->get_id();
            $sessions->ip = $_SERVER['REMOTE_ADDR'];
            $sessions->user_agent = $_SERVER['HTTP_USER_AGENT'];
            $sessions->date_start = App::CurrentDate(includes_variable::DB_DATETIME_FORMAT);
            // Определяем срок жизни токена. 30 дней или 8 часов
            $addTime = isset($_POST['save_me']) ? core_ui::LONG_SESSION : core_ui::SHORT_SESSION;
            $sessions->date_end = App::addHour($sessions->date_start, $addTime, includes_variable::DB_DATETIME_FORMAT);
            if(null !== $sessions->store()) {
                core_view_system::getInstance()->get('APPUI')->set_error('Не удалось создать сессию. Обратитесь к системному администратору');
                break;
            }
            setcookie('auth', $sessions->token); // Устанавливаем в куки токен
            $res = true;
        } while(false);

        return $res;
    }
}
?>