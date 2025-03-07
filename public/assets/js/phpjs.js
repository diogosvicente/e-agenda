


/*
 * Function : dump()
 * Arguments: The data - array,hash(associative array),object
 *    The level - OPTIONAL
 * Returns  : The textual representation of the array.
 * This function was inspired by the print_r function of PHP.
 * This will accept some data as the argument and return a
 * text that will be a more readable version of the
 * array/hash/object that is given.
 */
function debug(arr, level) {
    return dump(arr, level);
}

function dump(arr, level) {
    var dumped_text = "";
    if (!level) {
        level = 0;
    }

    /*The padding given at the beginning of the line.*/
    var level_padding = "";
    for (var j = 0; j < level + 1; j++) {
        level_padding += "    ";
    }

    if (arr.constructor == Array || arr.constructor == Object) {
        /*Array/Hashes/Objects*/
        for (var item in arr) {
            var value = arr[item];
            if (value.constructor == Array || value.constructor == Object) {
                /*If it is an array,*/
                /*			dumped_text += level_padding + "[" + item + "] " + typeof(theObj) + " ...\n";*/
                /*			dumped_text += level_padding + "[" + item + "] " + typeof(item) + " ...\n";*/
                dumped_text += level_padding + "[" + item + "] " + ((value.constructor == Array) ? "Array" : "Object") + " ...\n";
                dumped_text += dump(value, level + 1);
            } else {
                dumped_text += level_padding + "[" + item + "] => " + value + "\n";
            }
        } /* fim for(var item in arr) */
    } else {
        /*Stings/Chars/Numbers etc.*/
        dumped_text = "(" + typeof (arr) + ") var: " + arr + "\n";
    }
    return dumped_text;
} /* fim function dump(arr,level) */


function array_key_exists(key, search) {
    //  discuss at: http://phpjs.org/functions/array_key_exists/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Felix Geisendoerfer (http://www.debuggable.com/felix)
    //   example 1: array_key_exists('kevin', {'kevin': 'van Zonneveld'});
    //   returns 1: true

    if (!search || (search.constructor !== Array && search.constructor !== Object)) {
        return false;
    }

    return key in search;
}


function in_array(needle, haystack, argStrict) {
    //  discuss at: http://phpjs.org/functions/in_array/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: vlado houba
    // improved by: Jonas Sciangula Street (Joni2Back)
    //    input by: Billy
    // bugfixed by: Brett Zamir (http://brett-zamir.me)
    //   example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);
    //   returns 1: true
    //   example 2: in_array('vlado', {0: 'Kevin', vlado: 'van', 1: 'Zonneveld'});
    //   returns 2: false
    //   example 3: in_array(1, ['1', '2', '3']);
    //   example 3: in_array(1, ['1', '2', '3'], false);
    //   returns 3: true
    //   returns 3: true
    //   example 4: in_array(1, ['1', '2', '3'], true);
    //   returns 4: false

    var key = '',
            strict = !!argStrict;

    //we prevent the double check (strict && arr[key] === ndl) || (!strict && arr[key] == ndl)
    //in just one for, in order to improve the performance 
    //deciding wich type of comparation will do before walk array
    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true;
            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) {
                return true;
            }
        }
    }

    return false;
}

function sleep(segundos){
        
    var dataFutura = new Date().getTime() + (segundos * 1000);

    while (new Date().getTime() < dataFutura) {
    }
    return true;
}

function usleep(milisegundos){
        
    var dataFutura = new Date().getTime() + milisegundos;

    while (new Date().getTime() < dataFutura) {
    }
    return true;
}

function time_sleep_until(timestamp) { // eslint-disable-line camelcase
    //  discuss at: http://locutus.io/php/time_sleep_until/
    // original by: Brett Zamir (http://brett-zamir.me)
    //      note 1: For study purposes. Current implementation could lock up the user's browser.
    //      note 1: Expects a timestamp in seconds, so DO NOT pass in a JavaScript timestamp which are in milliseconds (e.g., new Date()) or otherwise the function will lock up the browser 1000 times longer than probably intended.
    //      note 1: Consider using setTimeout() instead.
    //   example 1: time_sleep_until(1233146501) // delays until the time indicated by the given timestamp is reached
    //   returns 1: true

    while (new Date() < timestamp * 1000) {
    }
    return true;
}