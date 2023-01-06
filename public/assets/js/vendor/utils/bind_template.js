/**
 * let html = BindTemplate.bind($('[template=example]').html(), {key : value});
 *
 * <script type="text/plain" template="example"></script>
 *
 * bind array into string, format index string => value
 * @param string receiver
 * @param array or object data for bind
 */

if ('undefined' == typeof BindTemplate) {
    var BindTemplate = {};
}

BindTemplate.bind = function (string, array, bracket) {
    var obj, i;

    typeof bracket == 'undefined' ? bracket = {} : null;
    typeof bracket.OPEN_BRACKET == 'undefined' ? bracket.OPEN_BRACKET = '{' : null;
    typeof bracket.CLOSE_BRACKET == 'undefined' ? bracket.CLOSE_BRACKET = '}' : null;
    // extra attribute into variables
    obj = bracket;
    for (i in obj) {
        if (typeof i != 'string') {
            continue;
        }
        var svar = '';
        eval("typeof " + i + " == 'undefined' ? svar = 'var' : svar = '';");
        eval(svar + ' ' + i + '=obj[i];');
    }
    // extra attribute into variables
    obj = array;
    for (i in obj) {
        if (typeof i != 'string') {
            continue;
        }
        svar = '';
        eval("typeof " + i + " == 'undefined' ? svar = 'var' : svar = '';");
        eval(svar + ' ' + i + '=obj[i];');
    }

    // split each element at format: ..{string}.... => string}....
    var opens = string.split(OPEN_BRACKET);
    // if there has character '{'
    if (opens.length > 1) {
        // browse each element
        for (var index in opens) {
            open = opens[index];
            // split each element string}.... => string...
            var closes = open.split(CLOSE_BRACKET);
            if (closes.length > 1) {
                // replace by value
                eval('closes[0] = ' + closes[0]);
                // composite parts
                opens[index] = closes.join('');
            }
        }
        // composite parts
        string = opens.join('');
    }
    return string;
}
