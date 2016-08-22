import React from 'react';
import Menu from './Menu';
import Isin from './Isin/Isin';

export default class DataEditor extends React.Component {
    constructor() {
        super();
        this.state = {
            currentView : "isin"
        };
        this.allViews = [
            {id : "isin", title : "Add/Edit ISIN"},
            {id : "isin-bulk", title : "Bulk upload ISIN"},
            {id : "hierachy-bulk", title : "Bulk upload hiearchy"},
            {id : "indices", title : "Indices"}
        ];
    };

    changeView(newViewId) {
        this.setState({currentView: newViewId});
    }

    render() {
        let contentArea;

        switch (this.state.currentView) {
            case 'isin-search':
                contentArea = (
                    <IsinSearch />
                );
                break;
            case 'isin':
            default:
                contentArea = (
                    <Isin />
                );
        }

        return (
            <div className="grid">
                <div className="g 1/5">
                    <Menu
                        onChangeView={this.changeView.bind(this)}
                        currentView={this.state.currentView}
                        allViews = {this.allViews}
                    />
                </div>
                <div className="g 4/5">{contentArea}</div>
            </div>
        );
    };
}
