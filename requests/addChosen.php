<?php
class requests_addChosen extends core_query_manager
{
    public function request()
    {
        do {
            $contact = entity_contacts::id($this->post->get('id'));
            if (!$contact->get_init()) {
                $this->set_error('Контакт не найден');
                break;
            }
            $AppUI = core_view_system::getInstance()->get('APPUI');
            $user = $AppUI->user->get_id();
            $check = entity_chosens::all(['sql' => 'id_user=:uid AND id_contact=:cui', 'param' => ['uid' => $user, 'cui' => $contact->get_id()]], null, 0, 1);
            if(isset($check[0])) {
                $this->set_error('Запись уже существует');
                break;
            }

            $chosen = new entity_chosens();
            $chosen->id_user = $user;
            $chosen->id_contact = $contact->get_id();

            if (null !== ($msg = $chosen->store())) {
                $this->set_error($msg);
                break;
            }

            unset($chosen);
        } while(false);
    }
}
?>