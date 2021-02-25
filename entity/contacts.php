<?php
class entity_contacts extends core_db
{
    public $name = null; // ФИО
	public $phone = null; // Телефон
	public $email = null; // E-Mail

    public function __construct($table = '', $key = '')
    {
        parent::__construct('contacts', 'ID');
    }
}
?>