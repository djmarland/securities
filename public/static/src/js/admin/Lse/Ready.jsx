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
            ISIN : item.isin
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
        let details = <Isin productOptions={window.ISIN.productOptions}
                            fieldValues={fieldValues} />;
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

class ReadyItem extends React.Component {
    ignore() {
        this.props.onIgnore(this.props.item.isin);
    }
    done() {
        this.props.onDone(this.props.item.isin);
    }

    render() {

    }
}