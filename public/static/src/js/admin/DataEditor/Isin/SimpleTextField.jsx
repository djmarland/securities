import React from 'react';
import BaseField from './BaseField';
import Status from './Status';

export default class SimpleTextField extends BaseField {
    getValue() {
        return this.refs.textInput.value;
    }

    handleInput() {
        let val = this.refs.textInput.value;
        this.setState({
            fieldText : val,
            statusType : null,
            statusText : null
        });
        if (val.length == 0 && this.props.isRequired) {
            this.setState({
                statusType : Status.STATUS_ERROR,
                statusText : 'Required'
            });
            this.props.onChange(this.props.id, val, false);
            return;
        }

        if (val.length > 0 && this.props.regex) {
            let regex = new RegExp(this.props.regex);
            if (!regex.test(val)) {
                this.setState({
                    statusType : Status.STATUS_ERROR,
                    statusText : 'Invalid data'
                });
                this.props.onChange(this.props.id, val, false);
                return;
            }
        }

        if (val.length > 0) {
            this.setState({
                statusType : Status.STATUS_OK,
                statusText : 'OK'
            });
        }
        this.props.onChange(this.props.id, val, true);
    }

    render() {
        return (
            <div className="form__group">
                <label htmlFor={this.fieldId} className="form__label">{this.props.label}</label>
                <input className="form__input" id={this.fieldId}
                       disabled={this.state.disabled}
                       value={this.state.fieldText}
                       ref="textInput"
                       required={this.props.isRequired}
                       onChange={this.handleInput.bind(this)}/>
                <div className="form__message">{this.getStatusElement()}</div>
            </div>
        );
    }
}