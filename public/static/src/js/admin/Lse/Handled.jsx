import React from 'react';
import Table from './Table';

export default class Handled extends React.Component {
    constructor() {
        super();
        this.state = {};
    };

    restoreISIN(isin) {
        this.props.onIsinToNew(isin);
    }

    render() {
        let items = [];
        this.props.items.forEach((item, i) => {
            items.push(<HandledItem key={i} item={item} onRestore={this.restoreISIN.bind(this)} />);
        });

        return (
            <ul className="list--lined">{items}</ul>
        );
    }
}

class HandledItem extends React.Component {
    restore() {
        this.props.onRestore(this.props.item.isin);
    }

    render() {
        let item = this.props.item;
        return(
            <li className="g-unit">
                <Table item={item} />
                <p className="text--right g-unit">
                    <button className="button button--fat" onClick={this.restore.bind(this)}>Move back to New</button>
                </p>
            </li>
        );
    }
}