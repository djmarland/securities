import React from 'react';
import IsinField from './IsinField';
import DateField from './DateField';
import SimpleTextField from './SimpleTextField';
import Status from './Status';
import Message from '../../Utils/Message';

export default class Isin extends React.Component {
    constructor() {
        super();
        this.state = {
            start : true,
            saving : false,
            messageType : null,
            messageText: null,
            invalidItems : []
        }
    };

    canBeSaved() {
        return (
            !this.state.start &&
            !this.state.saving &&
            Object.keys(this.state.invalidItems).length == 0
        );
    }

    onFormChange(id, value, valid) {
        let invalidItems = this.state.invalidItems;
        delete invalidItems[id];
        if (!valid) {
            invalidItems[id] = true;
        }
        this.setState({
            start : false,
            messageType : null,
            messageText: null,
            invalidItems : invalidItems
        })
    }

    onIsinChange(id, value, valid, security) {
        this.onFormChange(id, value, valid);
        if (security) {
            this.refs.SECURITY_NAME.setValue(security.name || '');
            this.refs.SECURITY_START_DATE.setValue(security.startDate || '');
            this.refs.MATURITY_DATE.setValue(security.maturityDate || '');
        }
    }

    onSave(e) {
        e.preventDefault();
        this.setState({
            saving: true,
            messageType : null,
            messageText: null
        });

        // prepare the ISIN
        let fields = [
            'ISIN',
            'SECURITY_NAME',
            'SECURITY_START_DATE',
            'MATURITY_DATE'
        ];

        let postData = {};
        fields.forEach(function(fieldId) {
           if (this.refs[fieldId]) {
               postData[fieldId] = this.refs[fieldId].getValue();
           }
        }.bind(this));

        // make an ajax call
        fetch('/admin/process-security.json', {
            method: 'post',
            body: JSON.stringify(postData),
            credentials: 'same-origin'
        })
            .then(function(response) {
                return response.json();
            }.bind(this))
            .then(function(data) {
                if (data.error) {
                    this.setState({
                        saving: false,
                        messageType : Message.TYPE_ERROR,
                        messageText : data.error
                    });
                    return;
                }

                this.setState({
                    saving: false,
                    messageType : Message.TYPE_OK,
                    messageText : 'Security saved successfully'
                });

            }.bind(this))
            .catch(function(err) {
                this.setState({
                    saving: false,
                    messageType : Message.TYPE_ERROR,
                    messageText : 'An error occurred saving the security'
                });
            }.bind(this));
    }

    render() {
        return (
            <form onSubmit={this.onSave.bind(this)}>
            <div className="grid">
                <div className="g 1/2">
                    <span className="e">* Required</span>
                </div>
                <div className="g 1/2">
                    <div className="text--right">
                        <Status isLoading={this.state.saving}/>
                        <button className="button button--fat"
                                type="submit"
                                disabled={!this.canBeSaved()}>
                            Save
                        </button>
                    </div>
                </div>
                <div className="g">
                    <Message
                        message={this.state.messageText}
                        type={this.state.messageType}
                    />
                </div>
                <div className="g 1/2">
                    <IsinField id="ISIN"
                               ref="ISIN"
                               onChange={this.onIsinChange.bind(this)}
                               label="Enter new ISIN or one to search for*"/>
                </div>
                <div className="g 1/2">
                    <SimpleTextField id="SECURITY_NAME"
                                     ref="SECURITY_NAME"
                                     onChange={this.onFormChange.bind(this)}
                                     isRequired={true}
                                     label="Security Name*"/>
                </div>
                <div className="g 1/2">
                    <DateField id="SECURITY_START_DATE"
                               ref="SECURITY_START_DATE"
                               onChange={this.onFormChange.bind(this)}
                               isRequired={true}
                               label="Start Date*"/>
                </div>
                <div className="g 1/2">
                    <DateField id="MATURITY_DATE"
                               ref="MATURITY_DATE"
                               onChange={this.onFormChange.bind(this)}
                               isRequired={false}
                               label="Maturity Date"/>
                </div>
            </div>
            </form>
        );
    };
}