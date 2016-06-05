import React from 'react';
import Lightbox from './Lightbox';
import AutoComplete from './../utils/AutoComplete';

export default class Issuer extends React.Component {
    constructor() {
        super();
        // Initial state of the component
        this.state = {
            loading: false,
            modals : {
                issuer : false,
                country: false,
                group: false
            },
            issuer : this.getEmptyIssuer()
        };
        let ac = new AutoComplete('/issuers/%s.json', this.singleIssuer.bind(this))

        var noGroups = document.querySelectorAll('[data-js="fetch-issuer"]'),
            l = noGroups.length,
            i;
        for (i = 0; i < l; i++) {
            noGroups[i].addEventListener('click', function(e) {
                this.setState({loading:true});
                window.scrollTo(0, 0);
                ac.newValue(e.target.dataset.id);
            }.bind(this));
        }
    }

    singleIssuer(data) {
        this.setState({
            loading:false,
            issuer: data.issuer
        });
    }

    getEmptyIssuer() {
        return {
            name: null,
            id: null,
            country: {
                name: null
            },
            parentGroup: {
                name: null
            }
        };
    }

    modalOpen(type, e) {
        e.preventDefault();
        let modals = this.state.modals;
        modals[type] = true;
        this.setState({modals: modals})
    }

    clearIssuer(e) {
        this.setState({issuer: this.getEmptyIssuer()})
    }

    useIssuerCallback(issuer) {
        this.setState({
            issuer:issuer
        });
        this.modalCloseCallback('issuer');
    }

    useGroupCallback(group) {
        let issuer = this.state.issuer;
        issuer.parentGroup = group;
        this.setState({
            issuer:issuer
        });
        this.modalCloseCallback('group');
    }

    useCountryCallback(country) {
        let issuer = this.state.issuer;
        issuer.country = country;
        this.setState({
            issuer:issuer
        });
        this.modalCloseCallback('country');
    }

    modalCloseCallback(name) {
        let modals = this.state.modals;
        modals[name] = false;
        this.setState({
            modals: modals
        });
    }

    handleInputChange(prop, event) {
        let issuer = this.state.issuer;
        issuer[prop] = event.target.value;
        this.setState({
            issuer: issuer
        });
    }

    removeValue(prop, e) {
        e.preventDefault();
        let issuer = this.state.issuer,
            original = this.getEmptyIssuer();
        issuer[prop] = original[prop];
        this.setState({
            issuer: issuer
        });
    }

    render() {
        let content = (
            <div className="g-unit">
                <h2 className="c g-unit">Add / Edit Issuer</h2>
                <p className="text--right">
                    <button className="button" onClick={this.modalOpen.bind(this, 'issuer')}>
                        <span className="button__icon">
                            <svg
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                                xmlnsXlink="http://www.w3.org/1999/xlink">
                                <use xlinkHref="#icon-search"></use>
                            </svg>
                        </span>
                        <span className="button__text">Search for existing issuer</span>
                    </button>
                    {" "}
                    <button className="button" onClick={this.clearIssuer.bind(this)}>
                        <span className="button__icon">
                            <svg
                                viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg"
                                xmlnsXlink="http://www.w3.org/1999/xlink">
                                <use xlinkHref="#icon-close"></use>
                            </svg>
                        </span>
                        <span className="button__text">Clear form</span>
                    </button>
                </p>

                <form method="post">
                <div className="form__group">
                    <label className="form__label" for="field-name">Name</label>
                    <input className="form__input" id="field-name" name="field-name" required
                           onChange={this.handleInputChange.bind(this, 'name')}
                           value={this.state.issuer.name} />
                </div>
                <div className="form__group">
                    <label className="form__label" for="field-country"><em>Country</em></label>
                    <div className="grid">
                        <div className="g 2/3">
                             <input className="form__input" id="field-country" name="field-country"
                                   readOnly
                                   value={this.state.issuer.country ? this.state.issuer.country.name : null} />
                        </div>
                        <div className="g 1/6">
                            <button className="button" onClick={this.modalOpen.bind(this, 'country')}>
                                <span className="button__icon">
                                    <svg
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlnsXlink="http://www.w3.org/1999/xlink">
                                        <use xlinkHref="#icon-edit"></use>
                                    </svg>
                                </span>
                            </button>
                        </div>
                        <div className="g 1/6">
                            <button className="button" onClick={this.removeValue.bind(this, 'country')}>
                                <span className="button__icon">
                                    <svg
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlnsXlink="http://www.w3.org/1999/xlink">
                                        <use xlinkHref="#icon-close"></use>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div className="form__group">
                    <label className="form__label" for="field-group"><em>Parent Group</em></label>
                    <div className="grid">
                        <div className="g 2/3">
                            <input className="form__input" id="field-group" name="field-group"
                                   readOnly
                                   value={this.state.issuer.parentGroup ? this.state.issuer.parentGroup.name : null} />
                        </div>
                        <div className="g 1/6">
                            <button className="button" onClick={this.modalOpen.bind(this, 'group')}>
                                <span className="button__icon">
                                    <svg
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlnsXlink="http://www.w3.org/1999/xlink">
                                        <use xlinkHref="#icon-edit"></use>
                                    </svg>
                                </span>
                            </button>
                        </div>
                        <div className="g 1/6">
                            <button className="button" onClick={this.removeValue.bind(this, 'parentGroup')}>
                                <span className="button__icon">
                                    <svg
                                        viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg"
                                        xmlnsXlink="http://www.w3.org/1999/xlink">
                                        <use xlinkHref="#icon-close"></use>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                <div className="form__group">
                    <label className="form__label" for="field-id"><em>ID</em></label>
                    <input data-js="field-id" className="form__input" id="field-id" name="field-id" readOnly
                           value={this.state.issuer.id} />
                </div>
                    <p className="text--right g-unit">
                        <button id="save-isin" type="submit" className="button button--fat">
                            {this.state.issuer.id ? "Update" : "Save new"}
                        </button>
                    </p>
                </form>
                <Lightbox modalIsOpen={this.state.modals.issuer}
                          closeCallback={this.modalCloseCallback.bind(this, 'issuer')}
                          title="Search for issuer">
                    <IssuerFinder useCallback={this.useIssuerCallback.bind(this)} />
                </Lightbox>
                <Lightbox modalIsOpen={this.state.modals.country}
                          closeCallback={this.modalCloseCallback.bind(this, 'country')}
                          title="Select country">
                    <CountryFinder useCallback={this.useCountryCallback.bind(this)} />
                </Lightbox>
                <Lightbox modalIsOpen={this.state.modals.group}
                          closeCallback={this.modalCloseCallback.bind(this, 'group')}
                          title="Select parent group">
                    <GroupFinder useCallback={this.useGroupCallback.bind(this)} />
                </Lightbox>
            </div>
        );

        return this.state.loading ? <Loading /> : content;
    }
}

class Loading extends React.Component {
    render() {
        return (
            <div className="loading g--align-center">
                <svg className="loading__spinner" viewBox="-2 -2 70 70" xmlns="http://www.w3.org/2000/svg">
                    <circle className="loading__path" fill="none" strokeWidth="8" cx="33" cy="33" r="30"></circle>
                </svg>
            </div>
        )
    }
}

class IssuerFinder extends React.Component {
    constructor() {
        super();
        // Initial state of the component
        this.state = {
            fieldText   : '',
            empty       : true,
            loading     : false,
            resultData  : null
        };
        this.autocomplete = new AutoComplete('/search.json?q=%s', this.displayResults.bind(this));
    }

    handleSearchInput() {
        let val = this.refs.searchInput.value;
        if (val.length > 0) {
            this.setState({
                fieldText : val,
                empty: false,
                loading: true
            });
            this.autocomplete.newValue(val);
        } else {
            this.setState({
                fieldText : val,
                loading: false,
                empty: true
            });
        }
    }

    useCallback(issuer) {
        this.props.useCallback(issuer);
    }

    displayResults(results) {
        this.setState({
            loading: false,
            resultData : results.issuers
        });
    }

    render() {
        return (
            <div>
                <div className="form__group">
                    <input
                        type="text"
                        className="form__input"
                        placeholder="Enter part of an issuer name"
                        value={this.state.fieldText}
                        ref="searchInput"
                        onChange={this.handleSearchInput.bind(this)}
                    />
                </div>
                <div>
                    { this.state.empty ? null :
                        this.state.loading ?
                            <Loading /> :
                            <IssuerResults data={this.state.resultData} useCallback={this.useCallback.bind(this)} /> }
                </div>
            </div>
        );
    }
}

class GroupFinder extends React.Component {
    constructor() {
        super();
        // Initial state of the component
        this.state = {
            fieldText   : '',
            empty       : true,
            loading     : false,
            resultData  : null
        };
        this.autocomplete = new AutoComplete('/search.json?q=%s', this.displayResults.bind(this));
    }

    handleSearchInput() {
        let val = this.refs.searchInput.value;
        if (val.length > 0) {
            this.setState({
                fieldText : val,
                empty: false,
                loading: true
            });
            this.autocomplete.newValue(val);
        } else {
            this.setState({
                fieldText : val,
                loading: false,
                empty: true
            });
        }
    }

    useCallback(group) {
        this.props.useCallback(group);
    }

    useNew() {
        let val = this.refs.searchInput.value;
        this.props.useCallback({
            name : val
        });
    }

    displayResults(results) {
        this.setState({
            loading: false,
            resultData : results.groups
        });
    }

    render() {
        return (
            <div>
                <div className="form__group">
                    <div className="grid">
                        <div className="g 4/5">
                            <input
                                type="text"
                                className="form__input"
                                placeholder="Enter part of a parent group name or enter a new one"
                                value={this.state.fieldText}
                                ref="searchInput"
                                onChange={this.handleSearchInput.bind(this)}
                            />
                        </div>
                        <div className="g 1/5">
                            <button className="button button--fat" onClick={this.useNew.bind(this)}>Use new</button>
                        </div>
                    </div>
                </div>
                <div>
                    { this.state.empty ? null :
                        this.state.loading ?
                            <Loading /> :
                            <GroupResults data={this.state.resultData} useCallback={this.useCallback.bind(this)} /> }
                </div>
            </div>
        );
    }
}

class CountryFinder extends React.Component {
    constructor() {
        super();
        this.state = {
            fieldText   : '',
            loading     : true,
            resultData  : null
        };

        let autoComplete = new AutoComplete('/countries.json', this.displayResults.bind(this));
        autoComplete.newValue('');
    }

    handleSearchInput() {
        let val = this.refs.searchInput.value;
        this.setState({
            fieldText : val
        });
    }

    useCallback(group) {
        this.props.useCallback(group);
    }

    useNew() {
        let val = this.refs.searchInput.value;
        this.props.useCallback({
            name : val
        });
    }

    displayResults(results) {
        this.setState({
            loading: false,
            resultData : results.countries
        });
    }

    render() {
        return (
            <div>
                <div className="form__group">
                    <div className="grid">
                        <div className="g 4/5">
                            <input
                                type="text"
                                className="form__input"
                                placeholder="Enter a new country name"
                                value={this.state.fieldText}
                                ref="searchInput"
                                onChange={this.handleSearchInput.bind(this)}
                            />
                        </div>
                        <div className="g 1/5">
                            <button className="button button--fat" onClick={this.useNew.bind(this)}>Create new</button>
                        </div>
                    </div>
                </div>
                <div>
                    { this.state.loading ?
                            <Loading /> :
                            <CountryResults data={this.state.resultData} useCallback={this.useCallback.bind(this)} /> }
                </div>
            </div>
        );
    }
}

class IssuerResults extends React.Component {
    useCallback(issuer) {
        this.props.useCallback(issuer);
    }
    render(){
        let items = [];
        let data = this.props.data;
        data.forEach(function(issuer){
            items.push(<IssuerResult issuer={issuer} useCallback={this.useCallback.bind(this)} />);
        }.bind(this));
        if (items.length) {
            return (
                <ul className="list--lined">{items}</ul>
            );
        }
        return (
            <p className="text--center">No results</p>
        );
    }
}

class IssuerResult extends React.Component {
    useItem() {
        this.props.useCallback(this.props.issuer);
    }
    render(){
        return(
            <li>
                <div className="grid">
                    <a className="g 4/5" href={'/issuers/' + this.props.issuer.id}>{this.props.issuer.name}</a>
                    <button className="g 1/5 button button--fat" onClick={this.useItem.bind(this)}>Use</button>
                </div>
            </li>
        );
    }
}

class CountryResults extends React.Component {
    useCallback(country) {
        this.props.useCallback(country);
    }
    render(){
        let items = [];
        let data = this.props.data;
        data.forEach(function(country){
            items.push(<CountryResult country={country} useCallback={this.useCallback.bind(this)} />);
        }.bind(this));
        if (items.length) {
            return (
                <ul className="list--lined">{items}</ul>
            );
        }
        return (
            <p className="text--center">No results</p>
        );
    }
}

class CountryResult extends React.Component {
    useItem() {
        this.props.useCallback(this.props.country);
    }
    render(){
        return(
            <li>
                <div className="grid">
                    <p className="g 4/5">{this.props.country.name}</p>
                    <button className="g 1/5 button button--fat" onClick={this.useItem.bind(this)}>Use</button>
                </div>
            </li>
        );
    }
}

class GroupResults extends React.Component {
    useCallback(group) {
        this.props.useCallback(group);
    }
    render(){
        let items = [];
        let data = this.props.data;
        data.forEach(function(group){
            items.push(<GroupResult group={group} useCallback={this.useCallback.bind(this)} />);
        }.bind(this));
        if (items.length) {
            return (
                <ul className="list--lined">{items}</ul>
            );
        }
        return (
            <p className="text--center">No results</p>
        );
    }
}

class GroupResult extends React.Component {
    useItem() {
        this.props.useCallback(this.props.group);
    }
    render(){
        return(
            <li>
                <div className="grid">
                    <a className="g 4/5" href={'/groups/' + this.props.group.id}>{this.props.group.name}</a>
                    <button className="g 1/5 button button--fat" onClick={this.useItem.bind(this)}>Use</button>
                </div>
            </li>
        );
    }
}
