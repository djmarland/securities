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
            <div className="finder">
                <ul className="finder__list">{items}</ul>
            </div>
        );
    };
}

class MenuItem extends React.Component {
    changeView() {
        this.props.onChangeView(this.props.id);
    }

    render(){
        let itemClass = 'finder__item' +
            ((this.props.currentView == this.props.id) ? ' finder__active' : '');
        return(
            <li className={itemClass}>
                <a className="finder__link" href="#" onClick={this.changeView.bind(this)}>
                    <span className="finder__indicator finder__indicator--nodrop"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="8"></circle>
                    </svg></span>
                    <span className="finder__text">{this.props.title}</span>
                </a>
            </li>
        );
    }
}