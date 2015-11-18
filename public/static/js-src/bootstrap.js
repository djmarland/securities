(function() {
    "use strict";

    var spinner = document.getElementById('loading-spinner'),
        mainPage = document.getElementById('main-body');

    function startSearch(value) {
        var spin = document.importNode(spinner.content, true);
        mainPage.innerHTML = '';
        mainPage.appendChild(spin);
    }

    function init() {
        var searchContainer = document.getElementById('search-container'),
            continerEmpty = !searchContainer.hasChildNodes(),
            hasBegunSearch = false,
            searchForm = document.getElementById('search-form'),
            searchBox = document.getElementById('search-input');

        searchBox.addEventListener('keyup', function() {
            var value = searchBox.value.trim(),
                searchUrl;

            if (!value.length) {
                return;
            }
            if (continerEmpty) {
                searchContainer.appendChild(searchForm);
                searchBox.focus();
                continerEmpty = false;
            }

            searchUrl = '/search?q=' + value;
            //if (!hasBegunSearch) {
            //    window.history.pushState({}, '', searchUrl);
            //    hasBegunSearch = true;
            //} else {
            //    window.history.replaceState({}, '', searchUrl);
            //}
            // @todo - tiny delay after typing?
            startSearch(value);
        });
    }

    // Cut the mustard
    if (document.addEventListener &&
        window.history
    ) {
        init();
    }

})();