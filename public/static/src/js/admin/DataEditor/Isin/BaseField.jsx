import React from 'react';
import Status from './Status';

export default class BaseField extends React.Component {
    constructor() {
        super();
        this.fieldId = 'field-' + Math.floor((Math.random() * 10000));
        this.state = {
            fieldText : '',
            disabled : false,
            statusType : null,
            statusText : null
        };
    };

    disable() {
        this.setState({
            disabled: true
        });
    }

    enable() {
        this.setState({
            disabled: false
        });
    }

    setValue(val) {
        let isEmpty = (val.length == 0);
        this.setState({
            fieldText: val,
            statusType : Status.STATUS_OK,
            statusText : 'OK'
        });
        if (isEmpty) {
            this.validateRequired();
            return;
        }

        // if value was set externally, it's valid
        this.props.onChange(this.props.id, null, true);
    }

    validateRequired() {
        if (this.props.isRequired) {
            this.setState({
                statusType : Status.STATUS_ERROR,
                statusText : 'Required'
            });
            this.props.onChange(this.props.id, null, false);
            return;
        }
        this.props.onChange(this.props.id, null, true);
    }

    getStatusElement() {
        return (
            <Status
                type={this.state.statusType}
                message={this.state.statusText}
            />
        );
    }
}