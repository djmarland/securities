(function() {
    "use strict";

    var ID;
    /*
    var spinner = document.getElementById('loading-spinner'),
        mainPage = document.getElementById('main-body');

    function loadUrl(url, callback) {
        var request = new XMLHttpRequest();
        request.open('GET', url, true);

        request.onload = function() {
            // @todo - be better at this, more resiliant
            // @todo -and cancel previous request if typing continues
            if (this.status >= 200 && this.status < 400) {
                // Success!
                var resp = this.response;
                callback(this.response);
            }
        };

        request.onerror = function() {
            // There was a connection error of some sort
        };

        request.send();
    }

    function startSearch(value) {
        var spin = document.importNode(spinner.content, true),
            inc = '/search?q=' + value + '&format=inc';
        mainPage.innerHTML = '';
        mainPage.appendChild(spin);
        loadUrl(inc, function(response) {
            mainPage.innerHTML = response;
        })
    }

    function init() {
        var searchContainer = document.getElementById('search-container'),
            mastheadContent = document.getElementById('masthead-content'),
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
                mastheadContent.classList.remove('transparent');
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
    */
    function setupShortcuts() {
        ID = function(val) { return document.getElementById(val); };
    }

    function setupStickys() {
        var stickyElements = document.getElementsByClassName('finder-wrapper');
        for (var i = stickyElements.length - 1; i >= 0; i--) {
            Stickyfill.add(stickyElements[i]);
        }
    }

    function setupFinderPosition() {
        var activeItems = document.getElementsByClassName('finder__active'),
            finder = document.getElementsByClassName('finder'),
            len = activeItems.length,
            last;
        if (len < 2) {
            return;
        }
        last = activeItems[len - 1];
        finder[0].scrollTop = last.offsetTop - 64;
    }

    function setupFilterButton() {
        var button = ID('js-securities-filter-button'),
            filter = ID('filter-wrap');
        if (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                filter.classList.toggle('hidden');
            });
            if (window.location.hash.indexOf('filter') != -1) {
                filter.classList.remove('hidden');
            }
        }
    }

    function init() {
        setupShortcuts();
        setupStickys();
        setupFilterButton();
        setupFinderPosition();
    }

    // Cut the mustard
    if (
        document.getElementsByClassName &&
        document.addEventListener &&
        window.history
    ) {
        init();
    }
})();