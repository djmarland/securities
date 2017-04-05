import React from 'react';
import BaseField from './BaseField';

export default class SimpleCheckbox extends BaseField {
    getValue() {
        return !!this.state.fieldText;
    }

    handleInput() {
        const val = !this.state.fieldText;
        this.setState({
            fieldText : val
        });
        this.props.onChange(this.props.id, val, true);
    }

    render() {
        const checked = !!this.state.fieldText;

        return (
            <div className="form__group">
                <label htmlFor={this.fieldId} className="form__label">{this.props.label}</label>
                <input type="checkbox"
                       className="form__input form__input--checkbox"
                       ref="inputField"
                       id={this.fieldId}
                       checked={checked}
                       onChange={this.handleInput.bind(this)} />
            </div>
        );
    }
}