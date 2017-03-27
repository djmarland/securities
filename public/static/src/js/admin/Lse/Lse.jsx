import React from 'react';
import Handled from './Handled';
import Ready from './Ready';

export default class Lse extends React.Component {
    constructor() {
        super();
        this.state = {
            currentView: 'todo',
            toDoItems: [],
            doneItems: [],
            ignoredItems: []
        };
        this.storedData = {};
        this.announcementId = null;
    };

    componentDidMount() {
        this.announcementId = this.props.announceid;
        this.announcements = this.props.announcements;
        this.setupStorage();

        if (localStorage.getItem(this.announcementId) === null) {
            let ignoredOnInitial = 0;
            this.announcements.forEach((item) => {
                if (this.ignoreOnInitial(item)) {
                    ignoredOnInitial++;
                    this.onIsinToIgnored(item.isin);
                }
            });
            if (ignoredOnInitial) {
                alert(ignoredOnInitial + ' items automatically set to ignore');
            }
        }
        this.sortAnnouncements();
    }

    strContains(needle, haystack) {
        return haystack.toString().indexOf(needle) != -1;
    }

    ignoreOnInitial(item) {
        if (
            this.strContains('SOURCE PHYSICAL', item.issuer) ||
            this.strContains('BOOST', item.issuer) ||
            this.strContains('ETFS', item.issuer) ||
            this.strContains('ISHARES', item.issuer) ||
            this.strContains('ORDINARY SHARES', item.description) ||
            this.strContains('ETP ', item.description) ||
            this.strContains('ETF ', item.description)
        ) {
            return true;
        }
        return false;
    }

    sortAnnouncements() {
        let toDo = [];
        let done = [];
        let ignored = [];

        this.announcements.forEach((item) => {
            if (this.inArray(this.storedData.doneIsins, item.isin)) {
                done.push(item);
            } else if (this.inArray(this.storedData.ignoredIsins, item.isin)) {
                ignored.push(item);
            } else {
                toDo.push(item);
            }
        });
        this.setState({
            toDoItems : toDo,
            doneItems : done,
            ignoredItems : ignored
        });
    }

    inArray(array, item) {
        return (array.indexOf(item) != -1);
    }

    saveData() {
        localStorage.setItem(this.announcementId, JSON.stringify(this.storedData));
    }

    setupStorage() {
        // check local storage. remove anything too old
        this.storedData = JSON.parse(localStorage.getItem(this.announcementId)) || {
            doneIsins : [],
            ignoredIsins : []
        };
    }

    showTodo() {
        this.setState({currentView : 'todo'});
    }

    showDone() {
        this.setState({currentView : 'done'});

    }

    showIgnored() {
        this.setState({currentView : 'ignored'});
    }

    onIsinToNew(isin) {
        let index = this.storedData.doneIsins.indexOf(isin);
        if (index !== -1) {
            this.storedData.doneIsins.splice(index, 1);
        }
        index = this.storedData.ignoredIsins.indexOf(isin);
        if (index !== -1) {
            this.storedData.ignoredIsins.splice(index, 1);
        }
        this.saveData();
        this.sortAnnouncements();
    }

    onIsinToDone(isin) {
        this.storedData.doneIsins.push(isin);
        this.storedData.doneIsins = this.storedData.doneIsins.filter((item, pos) => {
            return this.storedData.doneIsins.indexOf(item) == pos;
        });
        this.saveData();
        this.sortAnnouncements();
    }

    onIsinToIgnored(isin) {
        this.storedData.ignoredIsins.push(isin);
        this.storedData.ignoredIsins = this.storedData.ignoredIsins.filter((item, pos) => {
            return this.storedData.ignoredIsins.indexOf(item) == pos;
        });
        this.saveData();
        this.sortAnnouncements();
    }

    render() {
        let toDoCount = this.state.toDoItems.length;
        let doneCount = this.state.doneItems.length;
        let ignoredCount = this.state.ignoredItems.length;
        let toDoClass = 'tabs__tab';
        let doneClass = 'tabs__tab';
        let ignoredClass = 'tabs__tab';
        let activeClass = ' tabs__tab--active';
        let contentArea;
        switch (this.state.currentView) {
            case 'done':
                doneClass += activeClass;
                contentArea = <Handled items={this.state.doneItems} onIsinToNew={this.onIsinToNew.bind(this)} />;
                break;
            case 'ignored':
                ignoredClass += activeClass;
                contentArea = <Handled items={this.state.ignoredItems} onIsinToNew={this.onIsinToNew.bind(this)} />;
                break;
            default:
                toDoClass += activeClass;
                contentArea = <Ready items={this.state.toDoItems}
                                     onIsinToDone={this.onIsinToDone.bind(this)}
                                     onIsinToIgnore={this.onIsinToIgnored.bind(this)} />;
                break;
        }

        return (
            <div>
                <div className="tabs-wrap">
                    <ul className="tabs">
                        <li className={toDoClass}>
                            <button className="tabs__link" onClick={this.showTodo.bind(this)}>New ({toDoCount})</button>
                        </li>
                        <li className={doneClass}>
                            <button className="tabs__link" onClick={this.showDone.bind(this)}>Done ({doneCount})</button>
                        </li>
                        <li className={ignoredClass}>
                            <button className="tabs__link" onClick={this.showIgnored.bind(this)}>Ignored ({ignoredCount})</button>
                        </li>
                    </ul>
                </div>
                <div>
                    {contentArea}
                </div>
            </div>
        );
    };
}
