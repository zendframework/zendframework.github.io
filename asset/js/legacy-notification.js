(function () {
    "use strict";

    const createCookie = function (name, days) {
        if (name !== 'ALLOW_COOKIES' && ! getCookie('ALLOW_COOKIES')) {
            return;
        }
        const expires = new Date(new Date().getTime() + 1000 * 60 * 60 * 24 * days);
        const cookie = name + "=1;expires=" + expires.toGMTString() + ";";
        document.cookie = cookie;
    };

    const getCookie = function (name) {
        const re = new RegExp('(?:^|;\\s*)' + name + '=(.*?)(?:;|$)', 'g');
        const result = re.exec(document.cookie);
        return (result === null) ? null : result[1];
    };

    const showNotice = function () {
        if (getCookie('LAMINAS_NOTIFIER') !== null) {
            return;
        }

        const text = 'Zend Framework is now the <a href="https://docs.laminas.dev">Laminas Project</a>. The navbar includes a link to the replacement for this package. Please update your bookmarks.<br /><br /><small>(Unless you accept cookies, this notice will appear on every page.)</small>';

        const notice = PNotify.notice({
            title: "We've moved!",
            text: text,
            textTrusted: true,
            hide: false,
            icons: 'fontawesome4',
            styling: 'bootstrap3',
            modules: {
                Animate: {
                    animate: true,
                    inClass: "swing",
                    outClass: "fadeOutUp",
                },
                Buttons: {
                    closer: false,
                    sticker: false
                }
            }
        });
        notice.on('click', function () {
            createCookie('LAMINAS_NOTIFIER', 7);
            notice.close();
        });
    };

    const showCookieBar = function () {
        if (getCookie('ALLOW_COOKIES')) {
            return;
        }
        $('.cookie-bar').removeClass('hidden');
        $('.cookie-button').click(function () {
            createCookie('ALLOW_COOKIES', 90);
            $('.cookie-bar').addClass('hidden');
        });
    };

    showCookieBar();
    showNotice();
})();
