import React from 'react';
import ReactDOM from 'react-dom';
import Issuer from './admin/Issuer';

(function() {
    "use strict";

    function init() {

        ReactDOM.render( <Issuer />, document.getElementById('issuer-editor') );

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
