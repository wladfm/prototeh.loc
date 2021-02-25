/**
 * Объект для отправки по ajax
 */
class sendObject {
    // Конструктор объекта
    constructor(param = []) {
        if(!param || !Array.isArray(param) || param.length == 0) return;
        // Инициализируем все поля объекта
        for (let i in param) {
            if(param[i] == 'err') {
                this.err = 'Ошибка создания объекта';
                return;
            }
            eval('this.' + param[i] + '=null');
        }
        this.err = false;
        // Проход по полям формы
        this.init();
    }

    // Инициализация объекта
    init() {
        for (let i in this) {
            let issetId = eval('document.getElementById(\'' + i + '\')');
            let issetName = eval('document.getElementsByName(\'' + i + '\').length > 0');
            if(!issetId && !issetName) continue;
            if(issetId) eval('this.' + i + '=$(\'#' + i + '\').val()');
            else {
                let is_input = eval('$(\'[name=' + i + ']\')[0].nodeName');
                let is_type = eval('$(\'[name=' + i + ']\').attr(\'type\')')
                    && (eval('$(\'[name=' + i + ']\').attr(\'type\')==\'radio\'') || eval('$(\'[name=' + i + ']\').attr(\'type\')==\'checkbox\''));
                if(is_input && is_type) {
                    eval('this.' + i + '=$(\'[name=' + i + ']:checked\').val()');
                } else {
                    eval('let tmp_arr=new Array(); this.' + i + '=new Array();');
                    let code = '$(\'[name=' + i + ']\').each(function() {tmp_arr.push($(this).val());})';
                    eval('for(let k=0;k<tmp_arr.length;k++) {this.' + i + '.push(tmp_arr[k]);}');
                }
            }
            let empty = eval('!this.' + i);
            if(empty) this.err = 'Не все поля заполнены: ' + i;
        }
    }

    // Возврат ошибки
    error() {
        if(this.err !== false) {
            console.dir(this.err);
        }
        return this.err;
    }

    // Отрпавка на сервер запроса
    query(path, func = null, _async = true, _return = false, print_error = true) {
        if(!path) return;
        let request = null;

        $.ajax({
            type: 'POST',
            url: '/query.php?' + path,
            data: this,
            cache: false,
            async: _async,
            dataType: 'json',
            success: function (response) {
                if(!response) {
                    message_box('Пустой ответ от сервера', true);
                    return;
                }
                if(response.error) {
                    if(print_error) {
                        message_box(response.error, true);
                    }
                    return;
                }
                request = response.data;
                if(is_function(func)) {
                    eval(func + "(" + JSON.stringify(response.data) + ")");
                }
            },
            error: function (xhr) {
                if(print_error) {
                    message_box(xhr.statusText + xhr.responseText, true);
                }
            },
        });
    }
}

$(function() {
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    if(prefersDark) {
        $('#favicon').attr('href', '/core/view/css/images/favicon_white.ico');
    }
});

/**
 * Функция инициализации такблицы DataTable
 * @param id - ИД DOM-элемента
 * @param numFirstOrder - номер столбца для первоначальной сортировки
 */
function initDataTable(id = 'datatable', numFirstOrder = 0, desc = false) {
    $("#" + id).DataTable({
        "language": {
            "url": "http://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Russian.json"
        },
        "order": [[ numFirstOrder, desc ? 'desc' : 'asc' ]],
        'columnDefs': [{
            'targets': "no-sort",
            'sorting': false,
            'searching': false,
        }],
    });
}

/**
 * Удаление DOM-элемента
 * @param elem
 * @returns {{parentNode}|*}
 */
function remove(elem) {
    elem = document.getElementById(elem);
    if(!elem) return;
    return elem.parentNode ? elem.parentNode.removeChild(elem) : elem;
}

/**
 * Проверяет или существует функция
 * @param func - имя функции
 * @returns {*|boolean}
 */
function is_function(func) {
    func = window[func];
    let get_type = {};
    return func && get_type.toString.call(func) === '[object Function]';
}

/**
 * проверка или является строка json
 * @param str - строка json
 * @returns {boolean}
 */
function is_json(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

/**
 * Сообщение на экране (свой alert)
 * @param message - сообщение
 * @param error - ошибка или нет (если отсутсвует, то просто alert)
 * @param reloadAfterHidden - нужно ли перегружать страницу после закрытия сообщения
 */
function message_box(message, error, reloadAfterHidden = false) {
    let class_button = 'c-btn c-btn--info';
    if(error === true) class_button = 'c-btn c-btn--danger';
    else if(error === false) class_button = 'c-btn c-btn--success';

    let code = '<div class="c-modal modal fade" id="message_alert" style="z-index: 2000" tabindex="-1" role="dialog" aria-labelledby="message_alert"' + (reloadAfterHidden === true ? ' data-reload="true"' : '') + '>';
    code += '<div class="c-modal__dialog modal-dialog" role="document">';
    code += '<div class="modal-content">';
    code += '<div class="c-card u-p-medium u-mh-auto" style="max-width: 500px">';
    if(error === true) code += '<h3 style="color: red">Ошибка</h3>';
    code += '<p>' + message + '</p>';
    code += '<button class="' + class_button + '" data-dismiss="modal" style="float: right">Закрыть</button>';
    code += '</div></div></div></div>';

    $('body').append(code);

    //при скрывании модалки - удаляем её
    $('#message_alert').on('hidden.bs.modal', function (e) {
        let reload = false;
        if(document.getElementById("message_alert").hasAttribute('data-reload')) {
            reload = true;
        }
        remove("message_alert");
        if(reload) {
            location.reload();
        }
    });
    //открываем модалку
    $('#message_alert').modal('show');
}

/**
 * Перегрузка страницы
 */
function reload_page() {
    location.href = location.href;
}