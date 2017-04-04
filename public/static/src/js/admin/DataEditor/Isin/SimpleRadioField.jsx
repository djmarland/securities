import React from 'react';
import BaseField from './BaseField';
import Status from './Status';

export default class SimpleRadioField extends BaseField {
    getValue() {
        return this.state.fieldText;
    }

    handleInput(val) {
        this.setState({
            fieldText : val
        });
        this.props.onChange(this.props.id, val, true);
    }

    isChecked(value) {
        return (value === this.state.fieldText);
    }

    render() {
        const items = this.props.options.map((option, i) => {
            return (
                <div className="g 1/2 1/3@s 1/6@xl" key={i}>
                    <label className="form__radio">
                        <input className="form__radio-input"
                               type="radio"
                               checked={this.isChecked(option.value)}
                               name={this.fieldId}
                               ref={option.value}
                               value={option.value}
                               onChange={() => {this.handleInput(option.value)}} />
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