(function() {
    "use strict";

    function init() {
        var searchContainer = document.getElementById('search-container'),
            continerEmpty = !searchContainer.hasChildNodes(),
            hasBegunSearch = false,
            searchForm = document.getElementById('search-form'),
            searchBox = document.getElementById('search-input'),
            spinner = document.getElementById('loading-spinner'),
            mainPage = document.getElementById('main-body');

        searchBox.addEventListener('keyup', function() {
            var value,
                searchUrl;
            if (continerEmpty) {
                searchContainer.appendChild(searchForm);
                searchBox.focus();
                continerEmpty = false;
            }

            value = searchBox.value;
            searchUrl = '/search?q=' + value;
            if (!hasBegunSearch) {
                window.history.pushState({}, '', searchUrl);
                hasBegunSearch = true;
            } else {
                window.history.replaceState({}, '', searchUrl);
            }
        });
    }

    // Cut the mustard
    if (document.addEventListener &&
        window.history
    ) {
        init();
    }

})();