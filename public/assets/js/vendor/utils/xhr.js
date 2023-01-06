var requestTimeout = {};
var CHECK_REQUEST_TIMEOUT = 1;

sendRequest = function (options, callbackFunc) {
    var now = new Date();
    if (!checkRequest(options.url, now)) {
        return false;
    }
    var ajaxOptions = {
        error: function (response, exception) {
            try {
                var responseX = JSON.parse(response.responseText);
                var msg = '';
                switch (true) {
                    case responseX.message != '':
                        msg = responseX.message;
                        break;
                    case exception === 'parsererror':
                        msg = 'Requested JSON parse failed.';
                        break;
                    case exception === 'timeout':
                        msg = 'Time out error.';
                        break;
                    case exception === 'abort':
                        msg = 'Ajax request aborted.';
                        break;
                    default:
                        msg = 'Uncaught Error.\n' + response.responseText;
                        break;
                }
            } catch (e) {
                msg = 'System error !';
            }
            var res = {
                data: {},
                code: response.status,
                ok: false,
                message: msg
            };
            return callbackFunc(res);
        },
        success: function (response) {
            response = JSON.parse(response);
            return callbackFunc(response);
        },
        type: 'post',
        dataType: 'text',
        crossDomain: true,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            showLoading();
        },
        complete: function () {
            hideLoading();
        }
    };
    $.extend(ajaxOptions, options);
    $.ajax(ajaxOptions);
};

checkRequest = function (url, time) {
    if (!requestTimeout._g(url, false)) {
        requestTimeout._s(url, time);
        return true;
    }
    var requestOldTime = requestTimeout._g(url, 0);
    if (time - requestOldTime >= CHECK_REQUEST_TIMEOUT) {
        requestTimeout._s(url, time);
        return true;
    }
    return false;
};
