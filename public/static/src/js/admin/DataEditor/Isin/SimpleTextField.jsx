import React from 'react';
import BaseField from './BaseField';
import Status from './Status';

export default class SimpleTextField extends BaseField {
    getValue() {
        return this.refs.textInput.value;
    }

    setValue(val) {
        this.setState({
            fieldText: val
        });
        this.handleInput();
    }


    handleInput() {
        let val = this.refs.textInput.value;
        this.setState({
            fieldText : val,
            statusMsg : null,
            isError : false,
            isOk : false,
        });
        if (val.length == 0 && this.props.isRequired) {
            this.setState({
                statusMsg: 'Required',
                isError: true,
            });
            this.props.onChange(this.props.id, val, false);
            return;
        }

        if (val.length > 0 && this.props.regex) {
            let regex = new RegExp(this.props.regex);
            if (!regex.test(val)) {
                this.setState({
                    statusMsg: 'Invalid data',
                    isError: true,
                });
                this.props.onChange(this.props.id, val, false);
                return;
            }
        }

        if (val.length > 0) {
            this.setState({
                statusMsg: 'Valid',
                isOk: true,
            });
        }
        this.props.onChange(this.props.id, val, true);
    }

    render() {
        let status = (
            <Status
                isError={this.state.isError}
                isOk={this.state.isOk}
                message={this.state.statusMsg}
            />
        );

        return (
            <div className="form__group">
                <label htmlFor={this.fieldId} className="form__label">{this.props.label}</label>
                <input className="form__input" id={this.fieldId}
                       value={this.state.fieldText}
                       ref="textInput"
                       required={this.props.isRequired}
                       onChange={this.handleInput.bind(this)}/>
                <div className="form__message">{status}</div>
            </div>
        );
    }
}