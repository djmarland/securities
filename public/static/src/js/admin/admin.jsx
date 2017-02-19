import React from 'react';
import ReactDOM from 'react-dom';
import DataEditor from './DataEditor/DataEditor';
import Lse from './Lse/Lse';
import Issuer from './Issuer';
import Compare from './Compare';

(function() {
    "use strict";

    function init() {
        var data = document.getElementById('data-editor'),
            issuer = document.getElementById('issuer-editor'),
            lse = document.getElementById('lse-editor'),
            compare = document.getElementById('compare-editor');

        if (data) {
            let productOptions = window.ISIN.productOptions || null;
            let bulkStats = window.ISIN.bulkStats || null;
            ReactDOM.render(<DataEditor productOptions={productOptions} bulkStats={bulkStats} />, data);
        } else if (issuer) {
            ReactDOM.render(<Issuer />, issuer);
        } else if (compare) {
            ReactDOM.render(<Compare />, compare);
        } else if (lse) {
            let announcements = JSON.parse(lse.dataset.lse);
            let id = lse.dataset.announceid;
            ReactDOM.render(<Lse announcements={announcements} announceid={id} />, lse);
        }

        // disable some events globally
        ['dragover', 'drop'].forEach(function(name) {
            window.addEventListener(name,function(e){
                e = e || event;
                e.preventDefault();
            },false);
        });
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
