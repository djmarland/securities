import React from 'react';

export default class BaseField extends React.Component {
    constructor() {
        super();
        this.fieldId = 'field-' + Math.floor((Math.random() * 10000));
        this.state = {
            fieldText : '',
            statusMsg : null,
            isError : false,
            isOk : false
        }
    };
}