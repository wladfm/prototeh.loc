<?php
class pages_list implements core_view_body
{
    public static function view()
    {
        $contacts = entity_contacts::all();
        $chosenIds = entity_chosens::getListIdChosensByUser();

        echo '<h3>HHHHHHHHHHHHHHHHHHH</h3>';

        echo '<div class="c-table-responsive@desktop">';
        echo '<table class="c-table" id="tableContacts">';
        echo '<thead class="c-table__head c-table__head--slim">';
        echo '<tr class="c-table__row">';
        echo '<th class="c-table__cell c-table__cell--head">ФИО</th>';
        echo '<th class="c-table__cell c-table__cell--head">Номер телефона</th>';
        echo '<th class="c-table__cell c-table__cell--head">E-MAIL</th>';
        echo '<th class="c-table__cell c-table__cell--head no-sort"></th>';
        echo '</tr></thead>';

        echo '<tbody>';
        foreach ($contacts as $contact) {
            echo '<tr>';
            echo '<td class="c-table__cell">' . $contact->name . '</td>';
            echo '<td class="c-table__cell">' . $contact->phone . '</td>';
            echo '<td class="c-table__cell">' . $contact->email . '</td>';
            echo '<td class="c-table__cell">' .
                (in_array($contact->get_id(), $chosenIds)
                    ? '<i class="fa fa-trash" onclick="del(' . $contact->get_id() . ')"></i>' : '<i class="fa fa-plus" onclick="add(' . $contact->get_id() . ')"></i>') . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';

        echo '</table>';
        echo '</div>';
    }
}
?>