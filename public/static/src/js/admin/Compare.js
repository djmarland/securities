import React from 'react';

export default class Compare extends React.Component {
    constructor() {
        super();
        this.state = {
            sourceData: window.sourceIsins,
            inputField: '',
            inputData: '',
            calculating : false
        }
    };

    handleInputChange(event) {
        let val = event.target.value;
        this.setState({
            inputField: val,
            calculating: true
        });
        setTimeout(function() {
            this.setState({
                inputData: val,
                calculating: false
            });
        }.bind(this), 100);
    }

    getInput() {
        let text = this.state.inputData;
        return text.split(/\n/);
    }

    compareItems() {
        let input = this.getInput(),
            notInSource = this.diff(
                input,
                this.state.sourceData
            ),
            notInInput = this.diff(
                this.state.sourceData,
                input
            ),
            items = [];

        notInSource.forEach(function(item) {
            if (item.length == 0) { return }
            items.push(<li>{item} (not in database)</li>);
        });

        notInInput.forEach(function(item) {
            if (item.length == 0) { return }
            items.push(<li>{item} (not in input)</li>);
        });

        return items;
    }

    diff(a, b) {
        return a.filter(function(i) {
            return b.indexOf(i) < 0;
        });
    }

    render() {
        let styles = {height: '400px',overflow: 'auto'},
            sourceItems = [],
            comparedItems = this.state.calculating ? [] : this.compareItems();

        this.state.sourceData.forEach(function(item, i) {
           sourceItems.push(<li>{item}</li>);
        });

        let result = (<p>Enter data to see result</p>);

        if (this.state.calculating) {
            result = (
                <p>Calculating...</p>
            );
        } else {
            if (this.state.inputData.length > 0) {
                if (comparedItems.length == 0) {
                    result = (
                        <p>Perfect match</p>
                    );
                } else {
                    result = (
                        <ol>
                            {comparedItems}
                        </ol>
                    );
                }
            }
        }

        return (
            <div className="grid">
                <div className="g 1/3">
                    <h2 className="g-unit">ISINs in database</h2>
                    <div style={styles}>
                        <ol>
                            {sourceItems}
                        </ol>
                    </div>
                </div>
                <div className="g 1/3">
                    <h2 className="g-unit">Enter list to compare</h2>
                    <textarea
                        onChange={this.handleInputChange.bind(this)}
                        value={this.state.inputField}
                        style={styles}></textarea>
                </div>
                <div className="g 1/3">
                    <h2 className="g-unit">Results</h2>
                    <div style={styles}>
                        {result}
                    </div>
                </div>
            </div>
        );
    }
}