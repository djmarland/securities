import React from 'react';
import BaseField from './BaseField';
import Status from './Status';

export default class SimpleSelectField extends BaseField {
    getValue() {
        return this.refs.inputField.value;
    }

    handleInput() {
        let val = this.refs.inputField.value;
        this.setState({
            fieldText : val
        });
        this.props.onChange(this.props.id, val, true);
    }

    render() {
        let items = [];
        items.push(
            <option key="" value=""/>
        );
        this.props.options.forEach(function(option, i) {
            items.push(
                <option key={option.value} value={option.value}>{option.label}</option>
            );
        }.bind(this));

        return (
            <div className="form__group">
                <label htmlFor={this.fieldId} className="form__label">{this.props.label}</label>
                <select className="form__input"
                        ref="inputField"
                        id={this.fieldId}
                        value={this.state.fieldText}
                        onChange={this.handleInput.bind(this)}>{items}</select>
            </div>
        );
    }
}