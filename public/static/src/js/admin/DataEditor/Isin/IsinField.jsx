import React from 'react';
import BaseField from './BaseField';
import Status from './Status';

export default class IsinField extends BaseField {

    constructor() {
        super();
        this.state.loading = false;
    }

    getValue() {
        return this.refs.isinInput.value;
    }

    handleInput() {
        let val = this.getValue().toUpperCase();
        this.setState({
            fieldText : val,
            statusType : null,
            statusText : null
        });
        this.props.onChange(this.props.id, val, false);
        if (val.length == 0) {
            this.setState({
                statusType : Status.STATUS_ERROR,
                statusText : 'Required'
            });
            return;
        }

        if (val.length != 12) {
            this.setState({
                statusType : Status.STATUS_ERROR,
                statusText : 'Must be 12 characters'
            });
            return;
        }
        this.setState({
            statusType : Status.STATUS_LOADING,
            statusText : 'Checking ISIN'
        });

        // make an ajax call to get the ISIN.
        fetch('/admin/securities-check/' + val + '.json', {
            method: 'get',
            credentials: 'same-origin'
        })
            .then(function(response) {
                return response.json();
            }.bind(this))
            .then(function(data) {
                if (data.status == 'error') {
                    this.setState({
                        statusType : Status.STATUS_ERROR,
                        statusText : 'Not a valid ISIN'
                    });
                    return;
                }

                if (data.status == 'found') {
                    this.setState({
                        statusType : Status.STATUS_OK,
                        statusText : 'ISIN found'
                    });

                    // send the data back to the main form, to complete all fields
                    this.props.onChange(this.props.id, val, true, data.security);
                    return;
                }

                if (data.status == 'new') {
                    this.setState({
                        statusType : Status.STATUS_NEW,
                        statusText : 'New ISIN'
                    });
                    this.props.onChange(this.props.id, val, true);
                    return;
                }

                this.setState({
                    statusType : Status.STATUS_ERROR,
                    statusText : 'ISIN could not be processed'
                });
                this.props.onChange(this.props.id, val, true);


            }.bind(this))
            .catch(function(err) {
                this.setState({
                    statusType : Status.STATUS_ERROR,
                    statusText : 'An error occurred checking this ISIN'
                });
            }.bind(this));

    }

    render() {
        return (
            <div className="form__group">
                <label htmlFor={this.fieldId} className="form__label">{this.props.label}</label>
                <input className="form__input" id={this.fieldId}
                       disabled={this.state.isLoading}
                       required={true}
                       value={this.state.fieldText}
                       ref="isinInput"
                       onChange={this.handleInput.bind(this)}/>
                <div className="form__message">{this.getStatusElement()}</div>
            </div>
        );
    }
}