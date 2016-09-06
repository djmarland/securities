import React from 'react';
import Menu from './Menu';
import Isin from './Isin/Isin';
import BulkUpload from './BulkUpload/BulkUpload';

export default class DataEditor extends React.Component {
    constructor() {
        super();
        let view = window.location.hash || 'isin';
        view = view.replace('#','');

        this.state = {
            currentView : view
        };
        this.allViews = [
            {id : "isin", title : "Add/Edit ISIN"},
            {id : "isin-bulk", title : "Bulk upload ISIN"},
            {id : "hierachy-bulk", title : "Bulk upload hiearchy"},
            // {id : "indices", title : "Indices"}
        ];
    };

    changeView(newViewId) {
        this.setState({currentView: newViewId});
    }

    render() {
        let contentArea;
        switch (this.state.currentView) {
            case 'isin-bulk':
                contentArea = (
                    <BulkUpload bulkStats={this.props.bulkStats} />
                );
                break;
            case 'isin':
            default:
                contentArea = (
                    <Isin productOptions={this.props.productOptions} />
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
