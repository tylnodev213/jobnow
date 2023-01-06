// object
var objectEmpty = {};
Object.defineProperty(Object.prototype, '_s', {
    value: function (key, data) {
        if (typeof  key == 'object') {
            Object.defineProperty(this, data, {
                value: data,
                writable: true,
                enumerable: true,
                configurable: true
            });
        } else {
            Object.defineProperty(this, key, {
                value: key,
                writable: true,
                enumerable: true,
                configurable: true
            });
            this[key] = data;
        }
        return this;
    },
    enumerable: false
});

Object.defineProperty(Object.prototype, '_g', {
    value: function (keyData, defaultValue) {
        defaultValue = typeof defaultValue == 'undefined' ? objectEmpty : defaultValue;
        var obj = this;
        for (var key in obj) {
            if (keyData != key) {
                continue
            }
            return this[key] ? this[key] : defaultValue;

        }
        return defaultValue;
    },
    enumerable: false
});

Object.defineProperty(Object.prototype, '_gF', {
    value: function (keyData) {
        var obj = this;
        for (var key in obj) {
            return this[key];
        }
        return undefined;
    },
    enumerable: false
});

Object.defineProperty(Object.prototype, '_tA', {
    value: function (getKey) {
        var data = this;
        var result = [];

        for (idx in data) {
            if (typeof data[idx] != 'undefined') {
                result.push(getKey ? idx + '=' + data[idx] : data[idx]);
            }
        }
        return result;
    },
    enumerable: false
});
Object.defineProperty(Array.prototype, '_g', {
    value: function (keyData, defaultValue) {
        defaultValue = typeof defaultValue == 'undefined' ? [] : defaultValue;
        var val = this;
        return typeof val[keyData] != 'undefined' ? val[keyData] : defaultValue;
    },
    enumerable: false
});

// array
Object.defineProperty(Array.prototype, "_tO", {
    enumerable: false,
    writable: true,
    value: function (v) {
        return $.extend({}, this);
    }
});

Object.defineProperty(Array.prototype, "_f", {
    enumerable: false,
    writable: true,
    value: function (defaultValue) {
        return this._g(0, defaultValue);
    }
});

Object.defineProperty(Array.prototype, "_l", {
    enumerable: false,
    writable: true,
    value: function (defaultValue) {
        return this._g(this.length - 1, defaultValue);
    }
});
// string

String.prototype.url_tO = function (prefix, operator) {
    var str = this;
    str = str.split(prefix);
    var result = {};
    var l = str.length;
    for (var i = 0; i < l; i++) {
        var r = str[i].split(operator);
        if (r[0] == '') {
            continue;
        }
        result._s(r[0], r[1]);
    }
    return result;
};
String.prototype.addParams = function (key, value) {
    var str = this.toString();
    if (typeof key === 'string') {
        str = setParam(str, key, value);
        return str;
    }
    for (idx in key) {
        str = setParam(str, idx, key[idx]);
    }
    return str;
};

String.prototype.ec = function () {
    var str = this;
    str = str.rpAll('\\n', newline);
    str = str.rpAll('<br />', '');
    return str;
};


String.prototype.rA = function (find, replace) {
    var result = this;
    do {
        var split = result.split(find);
        result = split.join(replace);
    } while (split.length > 1);
    return result;
};

// function
function getL(key, defaultValue) {
    try {
        var val = localStorage.getItem(key);
        return typeof val != 'undefined' ? val : typeof defaultValue != 'undefined' ? defaultValue : '';
    } catch (e) {

    }
    return defaultValue;
}

function getLO(key, defaultValue) {
    try {
        var r = getL(key, '{}');
        if (r == 'undefined') {
            return defaultValue;
        }
        r = JSON.parse(r);
        return r ? r : defaultValue;
    } catch (e) {

    }
    return defaultValue;
}


function setLO(key, defaultValue) {
    defaultValue = defaultValue == undefined ? {} : defaultValue;
    setL(key, JSON.stringify(defaultValue));
}

function setL(key, defaultValue) {
    defaultValue = defaultValue == undefined ? {} : defaultValue;
    localStorage.setItem(key, defaultValue);
}

function getLKey(key) {
    return getTabId() + '_' + key;
}

function getRandData(data) {
    data = resetObject(data);
    var r = random(0, count(data) - 1);
    delete data[r];
    data = resetObject(data);
    return data;
}

function count(object) {
    return Object.keys(object).length;
}

function random(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function getYMD() {
    var d = new Date();
    return d.getFullYear() + '' + d.getMonth() + '' + d.getDate();
}

function dateDiff(date1, date2) {
    var timeDiff = Math.abs(date2.getTime() - date1.getTime());
    return timeDiff;
}

function getDayDateDiff(date1, date2) {
    var timeDiff = dateDiff(date1, date2);
    return Math.ceil(timeDiff / (1000 * 3600 * 24));
}

function getMinDateDiff(date1, date2) {
    var timeDiff = dateDiff(date1, date2);
    return Math.ceil(timeDiff / (1000 * 60));
}

function scrollToBottom() {
    window.scrollTo(0, document.body.scrollHeight);
}

function scrollToTop() {
    window.scrollTo(0, 0);
}

function scrollToX(e) {
    try {
        jQuery('html, body').animate({
            scrollTop: jQuery(e).offset().top
        }, getSec(random(1, 2)));
    } catch (e) {

    }

}

var extend = function e(c) {
    c = cloneObject(c);
    for (var d = 1; d < arguments.length; ++d) {
        var a = arguments[d];
        if ("object" === typeof a) for (var b in a) a.hasOwnProperty(b) && (c[b] = "object" === typeof a[b] ? e({}, c[b], a[b]) : a[b])
    }
    return c
}
var arrayMerge = function e(c) {
    c = cloneObject(c);
    for (var d = 1; d < arguments.length; ++d) {
        var a = arguments[d];
        if ("object" === typeof a) for (var b in a) a.hasOwnProperty(b) && (c[b] = "object" === typeof a[b] ? e({}, c[b], a[b]) : a[b])
    }
    return c
}

function _toString(str) {
    return typeof str == 'undefined' ? '' : str + '';
}

function getParam(url, name, defaultValue) {
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return defaultValue;
    if (!results[2]) return defaultValue;
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function cloneObject(obj) {
    return $.extend(true, {}, obj);
}

function resetObject(obj) {
    var i = 0;
    var newO = {};
    for (var b in obj) {
        obj.hasOwnProperty(b);
        newO[i] = obj[b];
        i++;
    }
    return newO;
}

function inObject(need, obj) {
    if (Object.values(obj).indexOf(need) > -1) {
        return true;
    }
    return false;
}

function inArray(need, array) {
    if (array.indexOf(need) > -1) {
        return true;
    }
    return false;
}

function getCurrentUrl() {
    return document.URL;
}

function redirect(url) {
    return window.location.href = url;
}

function empty(object) {
    try {
        return !count(object)
    } catch (e) {
        return true;
    }
}

function has(element) {
    return $(element).length > 0;
}

function hasVerticalScroll(node) {
    node = document.getElementById(node);
    return node.scrollTop > 20;
}

function ljust(string, width, padding) {
    padding = padding || " ";
    padding = padding.substr(0, 1);
    if (string.length < width)
        return string + padding.repeat(width - string.length);
    else
        return string;
}

function rjust(string, width, padding) {
    string = '' + string;
    padding = padding + '';
    padding = padding || " ";
    padding = padding.substr(0, 1);
    if (string.length < width)
        return padding.repeat(width - string.length) + string;
    else
        return string;
}

function center(string, width, padding) {
    padding = padding || " ";
    padding = padding.substr(0, 1);
    if (string.length < width) {
        var len = width - string.length;
        var remain = ( len % 2 == 0 ) ? "" : padding;
        var pads = padding.repeat(parseInt(len / 2));
        return pads + string + pads + remain;
    }
    else
        return string;
}

function changeUrl(params) {
    if (typeof params == 'undefined') {
        return;
    }
    if (typeof params == 'object') {
        params = params._tA(true).join('&');
    }
    params = encodeURIComponent(params);
    setL('old_params', document.location.search);
    window.history.pushState('', 'Title', '?' + params);
}

function getCurrentParams() {
    return decodeURIComponent(document.location.search.replace('?', '')).url_tO('&', '=');
}

function share_fb(url) {
    window.open('https://www.facebook.com/sharer/sharer.php?u=' + url, 'facebook-share-dialog', "width=626,height=436")
}

function now(format) {
    if (typeof format === 'undefined') {
        format = 'DD/MM/YYYY H:mm:ss'
    }
    return moment().format(format);
}

function getMin(time) {
    return time * 10000;
}

function getSec(time) {
    return time * 1000;
}

function setParam(uri, key, val) {
    return uri
        .replace(new RegExp("([?&]" + key + "(?=[=&#]|$)[^#&]*|(?=#|$))"), "&" + key + "=" + encodeURIComponent(val))
        .replace(/^([^?&]+)&/, "$1?");
}
