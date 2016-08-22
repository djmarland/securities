import React from 'react';

export default class Menu extends React.Component {
    constructor() {
        super();
    };
    changeView(newViewId) {
        this.props.onChangeView(newViewId)
    }
    render() {

        let items = [],
            currentView = this.props.currentView,
            onChange = this.changeView.bind(this);

        this.props.allViews.forEach(function(i) {
            items.push(<MenuItem
                key={i.id}
                id={i.id}
                title={i.title}
                onChangeView={onChange}
                currentView={currentView} />);
        });

        return (
            <ul className="list--unstyled">{items}</ul>
        );
    };
}

class MenuItem extends React.Component {
    changeView() {
        this.props.onChangeView(this.props.id);
    }

    render(){
        let itemClass = (this.props.currentView == this.props.id) ?
                'color-grey-light' : '';
        return(
            <li className={itemClass}>
                <a href="#" onClick={this.changeView.bind(this)}>
                {this.props.title}
                </a>
            </li>
        );
    }
}