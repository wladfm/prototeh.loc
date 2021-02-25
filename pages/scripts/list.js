$(function() {
    initDataTable('tableContacts', 0);
});

function add(id) {
    let send = new sendObject();
    send.id = id;
    send.query('m=addChosen', 'reload_page');
}

function del(id) {
    let send = new sendObject();
    send.id = id;
    send.query('m=delChosen', 'reload_page');
}