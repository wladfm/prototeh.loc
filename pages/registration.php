<?php
class pages_registration implements core_view_body
{
    public static function view()
    {
        $reg = new static();
        if($reg->reg()) {
            core_ui::_redirect('m=login');
        }

        $error = core_view_system::getInstance()->get('APPUI')->get_error();

        $msg_error = $error !== null ? '<div class="c-alert c-alert--danger alert fade show">
                        <i class="c-alert__icon fa fa-times-circle"></i> ' . $error . '
                        <button class="c-close" data-dismiss="alert" type="button">&times;</button>
                    </div>' : '';

        echo '
            <div class="o-page o-page--center o-page__card">
                <div class="c-card u-mb-xsmall">
                    <header class="c-card__header u-pt-large">
                        <h1 class="u-h3 u-text-center u-mb-zero">Регистрация</h1>
                    </header>
                    
                    ' . $msg_error . '
            
                    <form class="c-card__body" method="post">
                        <div class="c-field u-mb-small">
                            <label class="c-field__label" for="name">ФИО пользователя</label>
                            <input class="c-input" type="username" id="name" name="name" placeholder="Введите ФИО">
                        </div>
                        
                        <div class="c-field u-mb-small">
                            <label class="c-field__label" for="login">Логин</label>
                            <input class="c-input" type="username" id="login" name="login" placeholder="Введите логин">
                        </div>
            
                        <div class="c-field u-mb-small">
                            <label class="c-field__label" for="pass">Пароль</label>
                            <input class="c-input" type="password" id="pass" name="pass" placeholder="Введите пароль">
                        </div>
                        
                        <div class="c-field u-mb-small">
                            <label class="c-field__label" for="pass">Повторите пароль</label>
                            <input class="c-input" type="password" id="pass" name="pass2" placeholder="Повторите пароль">
                        </div>
            
                        <button class="c-btn c-btn--info c-btn--fullwidth" type="submit">Регистрация</button>
                        
                    </form>
                </div>
            </div>';
    }

    public function reg()
    {
        $res = false;

        do {
            $data = $_POST;
            if(!isset($data['login']) && !isset($data['pass']) && !isset($data['pass2'])) break;
            if(!isset($data['login']) || empty($data['login'])) {
                core_view_system::getInstance()->get('APPUI')->set_error('Введите логин');
                break;
            }
            if(!isset($data['pass']) || empty($data['pass'])) {
                core_view_system::getInstance()->get('APPUI')->set_error('Введите пароль');
                break;
            }
            if(!isset($data['pass2']) || empty($data['pass2'])) {
                core_view_system::getInstance()->get('APPUI')->set_error('Повторите пароль');
                break;
            }
            if($data['pass'] != $data['pass2']) {
                core_view_system::getInstance()->get('APPUI')->set_error('Не совпадают введенных и повторный пароль');
                break;
            }
            $user = new entity_users();
            $user->login = $data['login'];
            $user->pass = $data['pass'];
            $user->name = $data['name'];
            if(null !== ($msg = $user->store())) {
                core_view_system::getInstance()->get('APPUI')->set_error($msg);
                break;
            }
            $res = true;
        } while(false);

        return $res;
    }
}
?>