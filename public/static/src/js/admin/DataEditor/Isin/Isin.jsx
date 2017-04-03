import React from 'react';
import IsinField from './IsinField';
import DateField from './DateField';
import SimpleTextField from './SimpleTextField';
import SimpleRadioField from './SimpleRadioField';
import AutoCompleteField from './AutoCompleteField';
import SimpleCheckbox from './SimpleCheckbox';
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
            this.refs.SOURCE.setValue(security.source || '');
            this.refs.COUPON_RATE.setValue(security.coupon || '');
            this.refs.MONEY_RAISED_GBP.setValue(security.amountRaised || '');
            this.refs.MONEY_RAISED_LOCAL.setValue(security.amountRaisedLocal || '');
            this.refs.TRADING_CURRENCY.setValue(security.currency || '');
            this.refs.MARGIN.setValue(security.margin || '');
            this.refs.MARK_AS_INTERESTING.setValue(security.isInteresting || false);

            if (security.issuer) {
                this.setDataFromIssuer(security.issuer);
            }

            let product = '';
            if (security.product) {
                product = security.product.number;
            }
            this.refs.PRA_ITEM_4748.setValue(product);
        }
    }

    onIssuerChange(id, data, valid) {
        this.onFormChange(id, data, valid);
        if (data) {
            this.setDataFromIssuer(data, true);
        } else {
            // this.refs.COUNTRY_OF_INCORPORATION.enable();
        }

    }

    setDataFromIssuer(issuer, excludeIssuerItself) {
        // let country = '';
        // if (issuer.country) {
        //     country = issuer.country.name;
        // }
        // this.refs.COUNTRY_OF_INCORPORATION.setValue(country);
        // this.refs.COUNTRY_OF_INCORPORATION.disable();

        if (excludeIssuerItself) {
            return;
        }
        this.refs.COMPANY_NAME.setValue(issuer.name);
    }

    onSave(e) {
        e.preventDefault();
        this.setState({
            saving: true,
            messageType : null,
            messageText: null
        });

        // prepare the ISIN data
        let fields = [
            'ISIN',
            'SECURITY_NAME',
            'SECURITY_START_DATE',
            'MATURITY_DATE',
            'SOURCE',
            'COUPON_RATE',
            'MONEY_RAISED_GBP',
            'MONEY_RAISED_LOCAL',
            'TRADING_CURRENCY',
            'MARGIN',
            'PRA_ITEM_4748',
            'COMPANY_NAME',
            'MARK_AS_INTERESTING'
            // 'COUNTRY_OF_INCORPORATION',
        ];

        let postData = {};
        fields.forEach(function(fieldId) {
           if (this.refs[fieldId]) {
               postData[fieldId] = this.refs[fieldId].getValue();
           }
        }.bind(this));

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

                if (this.props.onSave) {
                    this.props.onSave();
                }

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
        let saveButtonStatusType = null;
        if (this.state.saving) {
            saveButtonStatusType = Status.STATUS_LOADING;
        }
        let fieldValues = this.props.fieldValues || {};

        return (
            <form onSubmit={this.onSave.bind(this)}>
            <h1 className="b g-unit">Add/Edit ISIN</h1>
            <div className="grid">
                <div className="g 1/2">
                    <span className="e">* Required</span>
                </div>
                <div className="g 1/2">
                    <div className="text--right">
                        <Status type={saveButtonStatusType} />
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
                <div className="g">
                    <IsinField id="ISIN"
                               ref="ISIN"
                               onChange={this.onIsinChange.bind(this)}
                               value={fieldValues.ISIN || null}
                               label="ISIN: Enter new ISIN or one to search for*"/>
                </div>
                <div className="g">
                    <SimpleTextField id="SECURITY_NAME"
                                     ref="SECURITY_NAME"
                                     onChange={this.onFormChange.bind(this)}
                                     value={fieldValues.SECURITY_NAME || null}
                                     isRequired={true}
                                     label="SECURITY_NAME: Security Name*"/>
                </div>
                <div className="g 1/2@l">
                    <DateField id="SECURITY_START_DATE"
                               ref="SECURITY_START_DATE"
                               onChange={this.onFormChange.bind(this)}
                               value={fieldValues.SECURITY_START_DATE || null}
                               isRequired={true}
                               label="SECURITY_START_DATE: Start Date*"/>
                </div>
                <div className="g 1/2@l">
                    <DateField id="MATURITY_DATE"
                               ref="MATURITY_DATE"
                               onChange={this.onFormChange.bind(this)}
                               value={fieldValues.MATURITY_DATE || null}
                               isRequired={false}
                               label="MATURITY_DATE: Maturity Date"/>
                </div>
                <div className="g 1/2@l">
                    <SimpleTextField id="SOURCE"
                                     ref="SOURCE"
                                     onChange={this.onFormChange.bind(this)}
                                     value={fieldValues.SOURCE || null}
                                     label="SOURCE: Source"/>
                </div>
                <div className="g 1/2@l">
                    <SimpleTextField id="COUPON_RATE"
                                     ref="COUPON_RATE"
                                     regex="^[0-9.]+[%]?$"
                                     onChange={this.onFormChange.bind(this)}
                                     value={fieldValues.COUPON_RATE || null}
                                     label="COUPON_RATE: Coupon (decimal, or with %)"/>
                </div>
                <div className="g 1/2@l">
                    <SimpleTextField id="MONEY_RAISED_GBP"
                                     ref="MONEY_RAISED_GBP"
                                     regex="^[0-9.]+$"
                                     onChange={this.onFormChange.bind(this)}
                                     value={fieldValues.MONEY_RAISED_GBP || null}
                                     label="MONEY_RAISED_GBP: Money Raised (GBP £m)"/>
                </div>
                <div className="g 1/2@l">
                    <SimpleTextField id="MONEY_RAISED_LOCAL"
                                     ref="MONEY_RAISED_LOCAL"
                                     regex="^[0-9.]+$"
                                     onChange={this.onFormChange.bind(this)}
                                     value={fieldValues.MONEY_RAISED_LOCAL || null}
                                     label="MONEY_RAISED_LOCAL: Money Raised (Local Currency)"/>
                </div>
                <div className="g 1/2@l">
                    <SimpleTextField id="TRADING_CURRENCY"
                                     ref="TRADING_CURRENCY"
                                     regex="^[A-Z]{3}$"
                                     onChange={this.onFormChange.bind(this)}
                                     value={fieldValues.TRADING_CURRENCY || null}
                                     label="TRADING_CURRENCY: Trading Currency"/>
                </div>
                <div className="g 1/2@l">
                    <SimpleTextField id="MARGIN"
                                     ref="MARGIN"
                                     regex="^[0-9.]+[%]?$"
                                     onChange={this.onFormChange.bind(this)}
                                     value={fieldValues.MARGIN || null}
                                     label="MARGIN: Margin (decimal, or with %)"/>
                </div>
                <div className="g">
                    <SimpleRadioField id="PRA_ITEM_4748"
                                       ref="PRA_ITEM_4748"
                                       options={this.props.productOptions}
                                       onChange={this.onFormChange.bind(this)}
                                       value={fieldValues.PRA_ITEM_4748 || null}
                                       label="PRA_ITEM_4748: Product Type"/>
                </div>
                <div className="g 1/2@l">
                    <div>
                    <AutoCompleteField id="COMPANY_NAME"
                                       ref="COMPANY_NAME"
                                       sourceUrl="/admin/search.json?type=issuer&q={search}"
                                       value={fieldValues.COMPANY_NAME || null}
                                       onChange={this.onIssuerChange.bind(this)}
                                       label="COMPANY_NAME: Issuer Name"/>
                    {
                        (fieldValues.COMPANY_NAME) ? (
                            <p className="text--right">
                                <a href={'https://google.com?q=' + fieldValues.COMPANY_NAME}
                                   target="_blank">Search online for company &gt;</a>
                            </p>
                        ) : null
                    }
                    </div>
                </div>
                <div className="g 1/2@l">
                    <SimpleTextField id="PARENT_COMPANY"
                                       ref="PARENT_COMPANY"
                                       onChange={this.onFormChange.bind(this)}
                                       value={fieldValues.PARENT_COMPANY || null}
                                       label="PARENT_COMPANY"/>
                </div>
                <div className="g 1/2@l">
                    <SimpleTextField id="SECTOR"
                                     ref="SECTOR"
                                     onChange={this.onFormChange.bind(this)}
                                     value={fieldValues.SECTOR || null}
                                     label="SECTOR"/>
                </div>
                <div className="g 1/2@l">
                    <SimpleTextField id="INDUSTRY"
                                     ref="INDUSTRY"
                                     onChange={this.onFormChange.bind(this)}
                                     value={fieldValues.INDUSTRY || null}
                                     label="INDUSTRY"/>
                </div>
                <div className="g 1/2@l">
                    <SimpleCheckbox id="MARK_AS_INTERESTING"
                                     ref="MARK_AS_INTERESTING"
                                     onChange={this.onFormChange.bind(this)}
                                     value={fieldValues.MARK_AS_INTERESTING || false}
                                     label="MARK_AS_INTERESTING"/>
                </div>
                <div className="g">
                    <div className="text--right">
                        <Status type={saveButtonStatusType} />
                        <button className="button button--fat"
                                type="submit"
                                disabled={!this.canBeSaved()}>
                            Save
                        </button>
                    </div>
                </div>
            </div>
            </form>
        );
    };
}