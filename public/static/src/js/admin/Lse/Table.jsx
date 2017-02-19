import React from 'react';

export default class Table extends React.Component {
    render() {
        let item = this.props.item;
        let cells = [];
        item.cellContents.forEach((cell, i) => {
            cells.push(<td key={i}>{cell}</td>);
        });

        return (
            <table className="table table--striped g-unit">
                <thead>
                <tr>
                    <th colSpan={ item.colspan }>{ item.issuer }</th>
                </tr>
                </thead>
                <tbody>
                <tr>{cells}</tr>
                </tbody>
            </table>
        );
    }
}