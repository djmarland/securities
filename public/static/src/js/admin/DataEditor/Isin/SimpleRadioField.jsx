import React from 'react';
import BaseField from './BaseField';
import Status from './Status';

export default class SimpleRadioField extends BaseField {
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
        const items = this.props.options.map((option, i) => {
            return (
                <div className="g 1/3 1/6@xl" key={i}>
                    <label className="form__radio">
                    <input className="form__radio-input"
                           type="radio"
                           name={this.fieldId}
                           ref={option.value}
                           value={option.value}
                           onChange={this.handleInput.bind(this)} />
                        <span className="form__radio-label">{option.label}</span>
                    </label>
                </div>
            );
        });

        return (
            <div className="form__group">
                <p className="form__label">{this.props.label}</p>
                <div className="grid">
                    {items}
                </div>
            </div>
        );
    }
}