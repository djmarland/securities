import React from 'react';
import Status from './Status';

export default class IsinField extends React.Component {

    constructor() {
        super();
        this.state = {
            fieldText : '',
            statusMsg : null,
            isError : false,
            isOk : false,
            isNew : false,
            loading     : false
        }
    }

    handleInput() {
        let val = this.refs.isinInput.value;
        this.setState({
            fieldText : val,
            statusMsg : null,
            isError : false,
            isNew : false,
            isLoading : false,
            isOk : false,
        });
        this.props.onChange(this.props.id, val, false);
        if (val.length == 0) {
            this.setState({
                statusMsg : 'Required',
                isError : true,
            });
            return;
        }

        if (val.length != 12) {
            this.setState({
                statusMsg : 'Must be 12 characters',
                isError : true,
            });
            return;
        }
        this.setState({
            isLoading: true
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
                        statusMsg : 'Not a valid ISIN',
                        isError : true,
                        isLoading : false
                    });
                    return;
                }

                if (data.status == 'found') {
                    this.setState({
                        statusMsg : 'ISIN found',
                        isOk : true,
                        isLoading : false
                    });

                    // send the data back to the main form, to complete all fields
                    this.props.onChange(this.props.id, val, true, data.security);
                    return;
                }

                if (data.status == 'new') {
                    this.setState({
                        statusMsg : 'New ISIN',
                        isNew : true,
                        isLoading : false
                    });
                    this.props.onChange(this.props.id, val, true);
                    return;
                }

                this.setState({
                    statusMsg : 'ISIN could not be processed',
                    isError : true,
                    isLoading : false
                });
                this.props.onChange(this.props.id, val, true);


            }.bind(this))
            .catch(function(err) {
                this.setState({
                    statusMsg : 'An error occurred checking this ISIN',
                    isError : true,
                });
            }.bind(this));

    }

    render() {
        let status = (
            <Status
                isError={this.state.isError}
                isLoading={this.state.isLoading}
                isOk={this.state.isOk}
                isNew={this.state.isNew}
                message={this.state.statusMsg}
            />
        );

        return (
            <div className="form__group">
                <label htmlFor="field-isin" className="form__label">{this.props.label}</label>
                <div className="grid grid--flat">
                    <div className="g 2/3">
                        <input className="form__input" id="field-isin"
                               value={this.state.fieldText}
                               ref="isinInput"
                               onChange={this.handleInput.bind(this)}/>
                    </div>
                    <div className="g 1/3">{status}</div>
                </div>
            </div>
        );
    }
}