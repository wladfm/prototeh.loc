<?php
class entity_chosens extends core_db
{
    public $id_user = null; // ИД пользователя
    public $id_contact = null; // ИД контакта

    public function __construct($table = '', $key = '')
    {
        parent::__construct('chosens', 'ID');
    }

    /**
     * Возврат избранных контактов для текущего пользователя
     * @return array static
     */
    public static function getChosensByUser()
    {
        $res = [];

        do {
            $AppUI = core_view_system::getInstance()->get('APPUI');
            if(empty($AppUI->user)) break;

            $userId = $AppUI->user->get_id();
            $sql = "SELECT `contacts`.*
                    FROM `chosens`
                    JOIN `contacts` ON `contacts`.`ID` = `chosens`.`id_contact`
                    WHERE `chosens`.`id_user` = '" . intval($userId) . "'
                    ORDER BY `contacts`.`ID`";

            $res = entity_contacts::list($sql);
        } while(false);

        return $res;
    }

    /**
     * Список ИД избранных контактов для текущего пользователя
     * @return array int
     */
    public static function getListIdChosensByUser()
    {
        $res = [];

        do {
            $chosens = static::getChosensByUser();
            foreach ($chosens as $chosen) {
                $res[] = $chosen->get_id();
            }
        } while(false);

        return $res;
    }
}
?>