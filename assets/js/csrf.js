function getCookie(name) {
    var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    if (match) {
        return match[2];
    }
}

function setCsrfTokenInAllForms(csrfTokenName, csrfCookieName) {
    $('input[name="' + csrfTokenName + '"]').remove();
    var forms = document.querySelectorAll("form");
    forms.forEach(function (form) {
        var csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = csrfTokenName;
        csrfInput.value = getCookie(csrfCookieName);
        form.appendChild(csrfInput);
    });
}

$(document).ready(function () {
    // Add CSRF token input to each form and ajax requests
    var csrfTokenName = $('meta[name="csrf-token-name"]').attr('content');
    var csrfCookieName = $('meta[name="csrf-cookie-name"]').attr('content');

    setCsrfTokenInAllForms(csrfTokenName, csrfCookieName);

    $.ajaxPrefilter(function (options) {
        var method = (options.type || options.method || 'GET').toUpperCase();
        if (method === 'GET' || method === 'HEAD' || method === 'OPTIONS') {
            return;
        }

        var token = getCookie(csrfCookieName);
        if (!token || !csrfTokenName) {
            return;
        }

        if (typeof FormData !== 'undefined' && options.data instanceof FormData) {
            if (typeof options.data.has !== 'function' || !options.data.has(csrfTokenName)) {
                options.data.append(csrfTokenName, token);
            }
            return;
        }

        var encodedToken = $.param({ [csrfTokenName]: token });

        if (options.data === undefined || options.data === null || options.data === '') {
            options.data = encodedToken;
            if (options.contentType === undefined) {
                options.contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
            }
        } else if (typeof options.data === 'string') {
            if (options.data.indexOf(csrfTokenName + '=') === -1) {
                options.data += '&' + encodedToken;
            }
        } else if (typeof options.data === 'object') {
            options.data[csrfTokenName] = token;
        }
    });

    $.ajaxSetup({
        credentials: "include",
        complete: function () {
            setCsrfTokenInAllForms(csrfTokenName, csrfCookieName);
        }
    });
});
