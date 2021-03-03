(function (Drupal, Cookies) {
    'use strict';
    Drupal.behaviors.wmusersnapAttach = {
        attach: function (context, drupalSettings) {
            var apiKey = drupalSettings.wmusersnap.apiKey;
            if (!apiKey) {
                return;
            }

            if (!Cookies.get('usersnap_enable:' + document.location.host)) {
                return;
            }

            window.onUsersnapCXLoad = function (a) {
                a.init()
            };

            var script = document.createElement('script');
            script.async = 1;
            script.src = 'https://widget.usersnap.com/load/' + apiKey + '?onload\x3donUsersnapCXLoad';
            document.getElementsByTagName('head')[0].appendChild(script);
        }
    };
})(Drupal, window.Cookies);
