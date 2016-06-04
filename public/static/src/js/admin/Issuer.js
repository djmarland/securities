import React from 'react';
import Lightbox from './Lightbox';
import AutoComplete from './../utils/AutoComplete';

export default class Issuer extends React.Component {
    constructor() {
        super();
        // Initial state of the component
        this.state = {
            searchVisible: false,
            searchText: '',
            loading: false,
            empty : true,
            resultData : null,
            issuer : {
                name: null,
                id: null
            }
        };
        this.autocomplete = new AutoComplete('/search.json?q=%s', this.displayResults.bind(this))
    }

    searchButtonClick() {
        this.setState({searchVisible: true})
    }

    handleSearchInput() {
        let val = this.refs.searchInput.value;
        if (val.length > 0) {
            this.setState({
                searchText : val,
                empty: false,
                loading: true
            });
            this.autocomplete.newValue(val);
        } else {
            this.setState({
                searchText : val,
                loading: false,
                empty: true
            });
        }
    }

    useCallback(issuer) {
        this.setState({
            issuer:issuer,
            searchVisible: false
        });
    }

    displayResults(results) {
        this.setState({
            loading: false,
            resultData : results.issuers
        });
    }

    handleInputChange(prop, event) {
        let issuer = this.state.issuer;
        issuer[prop] = event.target.value;
        this.setState({
            issuer: issuer
        });
    }

    render() {
        return (
            <div className="g-unit">
                <h2 className="c g-unit">Add / Edit Issuer</h2>
                <p className="text--right">
                    <button className="button" onClick={this.searchButtonClick.bind(this)}>
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
                </p>
                <form method="post">
                <div className="form__group">
                    <label className="form__label" for="field-name">Name</label>
                    <input className="form__input" id="field-name" name="field-name" required
                           onChange={this.handleInputChange.bind(this, 'name')}
                           value={this.state.issuer.name} />
                </div>
                <div className="form__group">
                    <label className="form__label" for="field-id">ID</label>
                    <input data-js="field-id" className="form__input" id="field-id" name="field-id" readOnly
                           value={this.state.issuer.id} />
                </div>
                    <p className="text--right g-unit"><button id="save-isin" className="button button--fat">Save</button></p>
                </form>
                <Lightbox ref="issuerSearchLightbox" modalIsOpen={this.state.searchVisible} title="Search for issuer">
                    <div className="form__group">
                        <input
                            type="text"
                            className="form__input"
                            placeholder="Enter part of an issuer name"
                            value={this.state.searchText}
                            ref="searchInput"
                            onChange={this.handleSearchInput.bind(this)}
                        />
                    </div>
                    <div>
                        { this.state.empty ? null :
                            this.state.loading ?
                                <div className="loading g--align-center">
                                    <svg className="loading__spinner" viewBox="-2 -2 70 70" xmlns="http://www.w3.org/2000/svg">
                                        <circle className="loading__path" fill="none" strokeWidth="8" cx="33" cy="33" r="30"></circle>
                                    </svg>
                                </div> :
                                <Results data={this.state.resultData} useCallback={this.useCallback.bind(this)} /> }
                    </div>
                </Lightbox>
            </div>

        );
    }
}

class Results extends React.Component {
    useCallback(issuer) {
        this.props.useCallback(issuer);
    }
    render(){
        let items = [];
        let data = this.props.data;
        data.forEach(function(issuer){
            items.push(<IssuerResult issuer={issuer} useCallback={this.useCallback.bind(this)} />);
        }.bind(this));
        return(
            <ul className="list--lined">{items}</ul>
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
                <div className="grid g--align-center">
                    <a className="g 4/5" href={'/issuers/' + this.props.issuer.id}>{this.props.issuer.name}</a>
                    <button className="g 1/5 button button--fat" onClick={this.useItem.bind(this)}>Use</button>
                </div>
            </li>
        );
    }
}
