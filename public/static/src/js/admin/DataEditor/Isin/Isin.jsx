import React from 'react';
import IsinField from './IsinField';
import DateField from './DateField';
import Status from './Status';

export default class Isin extends React.Component {
    constructor() {
        super();
        this.state = {
            start : true,
            saving : false,
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
            invalidItems : invalidItems
        })
    }

    onIsinChange(id, value, valid, security) {
        this.onFormChange(id, value, valid);
        if (security) {
            this.refs.START_DATE.setValue(security.startDate || '');
            this.refs.MATURITY_DATE.setValue(security.maturityDate || '');
        }
    }

    onSave() {
        this.setState({
            saving: true
        });
        setTimeout(function () {
            this.setState({
                saving: false
            })
        }.bind(this), 30000);
    }

    render() {
        return (
            <div className="grid">
                <div className="g 1/2">
                    <span className="e">* Required</span>
                </div>
                <div className="g 1/2">
                    <p className="text--right">
                        <Status isLoading={this.state.saving}/>
                        <button className="button button--fat"
                                onClick={this.onSave.bind(this)}
                                disabled={!this.canBeSaved()}>
                            Save
                        </button>
                    </p>
                </div>
                <div className="g 1/2">
                    <IsinField id="ISIN"
                               ref="ISIN"
                               onChange={this.onIsinChange.bind(this)}
                               label="Enter new ISIN or one to search for*"/>
                </div>
                <div className="g 1/2">
                    <DateField id="START_DATE"
                               ref="START_DATE"
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
        );
    };
}