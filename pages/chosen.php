<?php
class pages_chosen implements core_view_body
{
    public static function view()
    {
        $chosens = entity_chosens::getChosensByUser();

        echo '<div class="c-table-responsive@desktop">';
        echo '<table class="c-table" id="tableChosens">';
        echo '<thead class="c-table__head c-table__head--slim">';
        echo '<tr class="c-table__row">';
        echo '<th class="c-table__cell c-table__cell--head">ФИО</th>';
        echo '<th class="c-table__cell c-table__cell--head">Номер телефона</th>';
        echo '<th class="c-table__cell c-table__cell--head">E-MAIL</th>';
        echo '<th class="c-table__cell c-table__cell--head no-sort"></th>';
        echo '</tr></thead>';

        echo '<tbody>';
        foreach ($chosens as $chosen) {
            echo '<tr>';
            echo '<td class="c-table__cell">' . $chosen->name . '</td>';
            echo '<td class="c-table__cell">' . $chosen->phone . '</td>';
            echo '<td class="c-table__cell">' . $chosen->email . '</td>';
            echo '<td class="c-table__cell"><i class="fa fa-trash" onclick="del(' . $chosen->get_id() . ')"></i></td>';
            echo '</tr>';
        }
        echo '</tbody>';

        echo '</table>';
        echo '</div>';
    }
}
?>