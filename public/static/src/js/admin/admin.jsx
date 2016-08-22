import React from 'react';
import ReactDOM from 'react-dom';
import DataEditor from './DataEditor/DataEditor';
import Issuer from './Issuer';
import Compare from './Compare';

(function() {
    "use strict";

    function init() {
        var data = document.getElementById('data-editor'),
            issuer = document.getElementById('issuer-editor'),
            compare = document.getElementById('compare-editor');

        if (data) {
            ReactDOM.render(<DataEditor />, data);
        } else if (issuer) {
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
