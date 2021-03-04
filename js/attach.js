(function (Cookies) {
    'use strict';
    var apiKey = Cookies.get('usersnap_enable:' + document.location.host);
    if (!apiKey) {
        return;
    }

    window.onUsersnapCXLoad = function (a) {
        a.init()
    };

    var script = document.createElement('script');
    script.async = 1;
    script.src = 'https://widget.usersnap.com/load/' + apiKey + '?onload\x3donUsersnapCXLoad';
    document.getElementsByTagName('head')[0].appendChild(script);
})(window.Cookies);
