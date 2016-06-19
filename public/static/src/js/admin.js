import React from 'react';
import ReactDOM from 'react-dom';
import Issuer from './admin/Issuer';
import Compare from './admin/Compare';

(function() {
    "use strict";

    function init() {
        var issuer = document.getElementById('issuer-editor'),
            compare = document.getElementById('compare-editor');

        if (issuer) {
            ReactDOM.render(<Issuer />, issuer);
        } else if (compare) {
            ReactDOM.render(<Compare />, compare);
        }
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
