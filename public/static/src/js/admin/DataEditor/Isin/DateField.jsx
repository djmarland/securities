import React from 'react';
import BaseField from './BaseField';
import Status from './Status';

export default class DateField extends BaseField {
    getValue() {
        return this.refs.dateInput.value;
    }

    setValue(val) {
        this.setState({
           fieldText: val
        });
        this.handleInput();
    }

    handleInput() {
        let val = this.refs.dateInput.value;
        this.setState({
            fieldText : val,
            statusMsg : null,
            isError : false,
            isOk : false,
        });
        if (val.length == 0) {
            if (this.props.isRequired) {
                this.setState({
                    statusMsg : 'Required',
                    isError : true,
                });
                this.props.onChange(this.props.id, val, false);
            } else {
                this.props.onChange(this.props.id, val, true);
            }
            return;
        }

        if (this.isValidDate(val)) {
            this.setState({
                statusMsg : 'Valid',
                isOk : true,
            });
            this.props.onChange(this.props.id, val, true);
            return;
        }

        this.setState({
            statusMsg : 'Must be valid DD/MM/YYYY',
            isError : true,
        });
        this.props.onChange(this.props.id, val, false);
    }

    isValidDate(s) {
        if (!/^[0-3][0-9]\/[0-1][0-9]\/[0-9]{4}$/.test(s)) {
            return false;
        }

        let bits = s.split('/'),
            y = bits[2],
            m  = bits[1],
            d = bits[0],
            // Assume not leap year by default (note zero index for Jan)
            daysInMonth = [31,28,31,30,31,30,31,31,30,31,30,31];

        // If evenly divisible by 4 and not evenly divisible by 100,
        // or is evenly divisible by 400, then a leap year
        if ( (!(y % 4) && y % 100) || !(y % 400)) {
            daysInMonth[1] = 29;
        }
        return d <= daysInMonth[--m];
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
                       ref="dateInput"
                       required={this.props.isRequired}
                       onChange={this.handleInput.bind(this)}/>
                <div className="form__message">{status}</div>
            </div>
        );
    }
}