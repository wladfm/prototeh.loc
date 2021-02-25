<?php
class requests_delChosen extends core_query_manager
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
            $chosens = entity_chosens::all(['sql' => 'id_user=:uid AND id_contact=:cui', 'param' => ['uid' => $user, 'cui' => $contact->get_id()]], null, 0, 1);
            foreach ($chosens as $chosen) {
                if(null !== ($msg = $chosen->delete())) {
                    $this->set_error($msg);
                    break;
                }
            }
        } while(false);
    }
}
?>