<?php
include_once('core/core.php');
$request = core_ui::query();
// Чистим вывод на экран, если был
while (ob_get_level() !== 0) {
    ob_end_clean();
}
echo json_encode($request);
?>