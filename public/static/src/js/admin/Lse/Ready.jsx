import React from 'react';
import Isin from '../DataEditor/Isin/Isin';
import Table from './Table';

export default class Ready extends React.Component {
    constructor() {
        super();
        this.state = {};
    };

    ignore(isin) {
        this.props.onIsinToIgnore(isin);
    }
    done(isin) {
        this.props.onIsinToDone(isin);
    }

    getFieldValues(item) {
        return {
            ISIN : item.isin,
            SECURITY_NAME : item.description,
            SECURITY_START_DATE : item.date,
            MATURITY_DATE : item.endDate,
            SOURCE : 'LSE',
            COUPON_RATE : item.coupon,
            MONEY_RAISED_GBP : item.gbpAmount,
            MONEY_RAISED_LOCAL : item.localAmount,
            TRADING_CURRENCY : item.currency,
            MARGIN : '',
            PRA_ITEM_4748 : '',
            COMPANY_NAME : item.issuer
        };
    }

    render() {
        // just one in this bit
        if (!this.props.items[0]) {
            return null;
        }
        let item = this.props.items[0];
        let fieldValues = this.getFieldValues(item);
        let onClick = () => {this.ignore(item.isin)};
        let details = <Isin key={item.isin}
                            productOptions={window.ISIN.productOptions}
                            fieldValues={fieldValues}
                            onSave={() => this.done(item.isin)}/>;
        return(
            <div className="g-unit">
                <Table item={item} />
                <p className="text--right g-unit">
                    Check details below or <button className="button button--fat" onClick={onClick}>
                    Ignore
                </button>
                </p>
                <div>
                    {details}
                </div>
            </div>
        );
    }
}
