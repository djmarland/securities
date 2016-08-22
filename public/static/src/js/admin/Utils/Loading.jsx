import React from 'react';

export default class Loading extends React.Component {
    render() {
        return (
            <div className="loading g--align-center">
                <svg className="loading__spinner" xmlns="http://www.w3.org/2000/svg">
                    <circle className="loading__path" fill="none" strokeWidth="4" cx="14" cy="14" r="10"></circle>
                </svg>
            </div>
        )
    }
}