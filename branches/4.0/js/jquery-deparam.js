/*jQuery.deparam = function (value) {
    var
    // Object that holds names => values.
        params = {},
    // Get query string pieces (separated by &)
        pieces = value.split('&'),
    // Temporary variables used in loop.
        pair, i, l;

    // Loop through query string pieces and assign params.
    for (i = 0, l = pieces.length; i < l; i++) {
        pair = pieces[i].split('=', 2);
        // Repeated parameters with the same name are overwritten. Parameters
        // with no value get set to boolean true.
        params[decodeURIComponent(pair[0])] = (pair.length == 2 ?
            decodeURIComponent(pair[1].replace(/\+/g, ' ')) : true);
    }

    return params;
};


 var hash;
 var myJson = {};
 var hashes = url.slice(url.indexOf('?') + 1).split('&');
 for (var i = 0; i < hashes.length; i++) {
 hash = hashes[i].split('=');
 myJson[hash[0]] = hash[1];
 }
 return myJson;


*/
jQuery.deparam = function (querystring) {
    // remove any preceding url and split
    querystring = querystring.substring(querystring.indexOf('?')+1).split('&');
    var params = {}, pair, d = decodeURIComponent, i;
    // march and parse
    for (i = querystring.length; i > 0;) {
        pair = querystring[--i].split('=');
        params[d(pair[0])] = d(pair[1]);
    }

    return params;
};//--  fn  deparam