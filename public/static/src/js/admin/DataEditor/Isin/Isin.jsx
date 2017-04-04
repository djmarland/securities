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
            companySearch : null,
            messageType : null,
            messageText: null,
            invalidItems : []
        }
    };

    canBeSaved() {
        return (
            !this.state.start &&
            !this.state.saving &&
            Object.keys(this.state.invalidItems).length === 0
        );
    }

    onFormChange(id, value, valid) {
        let invalidItems = this.state.invalidItems;
        delete invalidItems[id];
        if (!valid) {
            invalidItems[id] = true;
        }
        let state = {
            start : false,
            messageType : null,
            messageText: null,
            invalidItems : invalidItems
        };
        if (id === 'COMPANY_NAME') {
            state.companySearch = this.refs['COMPANY_NAME'].getValue();
        }

        this.setState(state)
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
        this.refs.COUNTRY_OF_INCORPORATION.enable();
        this.refs.COMPANY_PARENT.enable();
        this.refs.ICB_SECTOR.enable();
        this.refs.ICB_INDUSTRY.enable();
        if (data) {
            this.setDataFromIssuer(data, true);
        }

    }

    setDataFromIssuer(issuer, excludeIssuerItself) {
        if (!excludeIssuerItself) {
            this.refs.COMPANY_NAME.setValue(issuer.name);
        }

        if (issuer.country) {
            this.refs.COUNTRY_OF_INCORPORATION.setValue(issuer.country.name);
            this.refs.COUNTRY_OF_INCORPORATION.disable();
        }

        if (issuer.parentGroup) {
            const parentGroup = issuer.parentGroup;
            this.refs.COMPANY_PARENT.setValue(parentGroup.name);
            this.refs.COMPANY_PARENT.disable();
            if (parentGroup.sector) {
                const sector = parentGroup.sector;
                this.refs.ICB_SECTOR.setValue(sector.name);
                this.refs.ICB_SECTOR.disable();
                if (sector.industry) {
                    this.refs.ICB_INDUSTRY.setValue(sector.industry.name);
                    this.refs.ICB_INDUSTRY.disable();
                }
            }

        }
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
            'MARK_AS_INTERESTING',
            'COMPANY_PARENT',
            'COUNTRY_OF_INCORPORATION',
            'ICB_SECTOR',
            'ICB_INDUSTRY'
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
        let companySearch = null;
        if (this.state.companySearch) {
            companySearch = (
                <p className="text--right">
                    <a href={'https://www.google.co.uk/search?q=' + this.state.companySearch}
                       target="_blank">Search online for company &gt;</a>
                </p>
            );
        }

        return (
            <form onSubmit={this.onSave.bind(this)}>
            <h1 className="b g-unit">
                Add/Edit ISIN
                <span className="e"> (* Required)</span>
            </h1>
            <div className="grid">
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
                    {companySearch}
                    </div>
                </div>
                <div className="g 1/2@l">
                    <SimpleTextField id="COUNTRY_OF_INCORPORATION"
                                     ref="COUNTRY_OF_INCORPORATION"
                                     onChange={this.onFormChange.bind(this)}
                                     value={fieldValues.COUNTRY_OF_INCORPORATION || null}
                                     label="COUNTRY_OF_INCORPORATION"/>
                </div>
                <div className="g 1/2@l">
                    <SimpleTextField id="COMPANY_PARENT"
                                       ref="COMPANY_PARENT"
                                       onChange={this.onFormChange.bind(this)}
                                       value={fieldValues.COMPANY_PARENT || null}
                                       label="COMPANY_PARENT"/>
                </div>
                <div className="g 1/2@l">
                    <SimpleTextField id="ICB_SECTOR"
                                     ref="ICB_SECTOR"
                                     onChange={this.onFormChange.bind(this)}
                                     value={fieldValues.ICB_SECTOR || null}
                                     label="ICB_SECTOR"/>
                </div>
                <div className="g 1/2@l">
                    <SimpleTextField id="ICB_INDUSTRY"
                                     ref="ICB_INDUSTRY"
                                     onChange={this.onFormChange.bind(this)}
                                     value={fieldValues.ICB_INDUSTRY || null}
                                     label="ICB_INDUSTRY"/>
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