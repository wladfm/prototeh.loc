$(function() {
    initDataTable('tableChosens', 0);
});

function del(id) {
    let send = new sendObject();
    send.id = id;
    send.query('m=delChosen', 'reload_page');
}