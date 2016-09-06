import React from 'react';
import BaseField from './BaseField';
import Status from './Status';

export default class AutoCompleteField extends BaseField {
    getValue() {
        return this.refs.textInput.value;
    }

    setValue(val) {
        let isEmpty = (val.length == 0);
        this.setState({
            itemSelected: -1,
            fieldText: val,
            valueOptions: null,
            statusType : Status.STATUS_OK,
            statusText : 'OK'
        });
        if (isEmpty) {
            this.validateRequired();
            return;
        }

        // if value was set externally, it's valid
        this.props.onChange(this.props.id, null, true);
    }

    handleInput() {
        if (this.timer) {
            clearTimeout(this.timer);
        }
        let val = this.refs.textInput.value,
            isEmpty = (val.length == 0);
        this.setState({
            fieldText : val,
            itemSelected: -1,
            valueOptions : null,
            statusType : Status.STATUS_LOADING,
            statusText : null
        });
        if (isEmpty) {
            this.validateRequired(val);
            return;
        }

        let url = this.props.sourceUrl;
        url = url.replace('{search}', val);

        this.timer = setTimeout(function() {
            this.performSearch(url);
        }.bind(this), 400);
    }

    performSearch(url, val) {

        // make an ajax call to get the ISIN.
        fetch(url, {
            method: 'get',
            credentials: 'same-origin'
        })
            .then(function(response) {
                return response.json();

            }.bind(this))
            .then(function(data) {
                if (data.results.length == 0) {
                    this.setState({
                        statusType : Status.STATUS_NEW,
                        statusText : 'New entry'
                    });
                    this.props.onChange(this.props.id, null, true);
                    return;
                }

                let autoValues = [],
                    matches = null;
                data.results.forEach(function(item) {
                    if (item.name == val) {
                        matches = item;
                    }
                    autoValues.push({
                        label : item.name,
                        data : item
                    });
                }.bind(this));

                if (matches) {
                    this.setState({
                        statusType : Status.STATUS_OK,
                        statusText : 'Entry exists. Re-using'
                    });

                    this.props.onChange(this.props.id, matches, true);
                    return;
                }

                if (autoValues.length == 0) {
                    this.setState({
                        statusType : Status.STATUS_NEW,
                        statusText : 'New Entry'
                    });
                } else {
                    this.setState({
                        statusType : Status.STATUS_NEW,
                        statusText: 'Choose entry',
                        valueOptions: autoValues
                    });
                }

                this.props.onChange(this.props.id, null, true);


            }.bind(this))
            .catch(function(err) {
                this.setState({
                    statusType : Status.STATUS_ERROR,
                    statusText : 'An error occurred'
                });
            }.bind(this));
    }

    handleAutoCompleteSelect(data) {
        this.setState({
            fieldText: data.name,
            valueOptions: null,
            itemSelected: -1,
            statusType : Status.STATUS_OK,
            statusText : 'OK'
        });
        this.props.onChange(this.props.id, data, true);
    }

    handleKey(event) {
        if (!this.state.valueOptions) {
            return; // nothing to do
        }
        let key = event.key,
            optionsCount = this.state.valueOptions.length;

        switch (key) {
            case 'ArrowDown':
                event.preventDefault();
                if (this.state.itemSelected < (optionsCount-1)) {
                    this.setState({
                        itemSelected: this.state.itemSelected + 1
                    })
                }
                break;
            case 'ArrowUp':
                event.preventDefault();
                if (this.state.itemSelected > -1) {
                    this.setState({
                        itemSelected: this.state.itemSelected - 1
                    });
                }
                break;
            case 'Enter':
                event.preventDefault();
                if (this.state.itemSelected > -1) {
                    this.handleAutoCompleteSelect(this.state.valueOptions[this.state.itemSelected].data);
                } else {
                    this.setState({
                        statusType : Status.STATUS_OK,
                        statusText: 'OK',
                        valueOptions: null
                    });
                }
                break;
        }
    }

    render() {
        let status = (
            <Status
                type={this.state.statusType}
                message={this.state.statusText}
            />
        );

        let autocomplete = null;
        if (this.state.valueOptions) {
            let items = [];
            this.state.valueOptions.forEach(function(item, i) {
                items.push(
                    <AutoCompleteItem
                        key={i}
                        data={item.data}
                        active={this.state.itemSelected == i}
                        onClick={this.handleAutoCompleteSelect.bind(this)}
                        label={item.label} />
                );
            }.bind(this));
            autocomplete = (
                <ul className="form__autocomplete">{items}</ul>
            );
        }

        return (
            <div className="form__group">
                <label htmlFor={this.fieldId} className="form__label">{this.props.label}</label>
                <input className="form__input" id={this.fieldId}
                       value={this.state.fieldText}
                       ref="textInput"
                       disabled={this.state.disabled}
                       required={this.props.isRequired}
                       onChange={this.handleInput.bind(this)}
                       onKeyDown={this.handleKey.bind(this)} />
                <div className="form__message">{status}</div>
                {autocomplete}
            </div>
        );
    }
}

class AutoCompleteItem extends React.Component {
    handleClick() {
        this.props.onClick(this.props.data);
    }

    render() {
        let className = 'form__autocomplete-item';
        if (this.props.active) {
            className += ' form__autocomplete-item--active'
        }

        return (
            <li className={className} onClick={this.handleClick.bind(this)}>{this.props.label}</li>
        );
    }
}