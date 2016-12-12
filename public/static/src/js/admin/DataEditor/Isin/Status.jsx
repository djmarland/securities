// import React from 'react';
import Loading from '../../Utils/Loading';

export default class Status extends React.Component {
    static get STATUS_OK() { return 'ok'; }
    static get STATUS_NEW() { return 'new'; }
    static get STATUS_ERROR() { return 'error'; }
    static get STATUS_LOADING() { return 'loading'; }
    render() {
        switch (this.props.type) {
            case Status.STATUS_LOADING:
                return (
                    <StatusLoading />
                );
            case Status.STATUS_ERROR:
                return (
                    <StatusError message={this.props.message} />
                );
            case Status.STATUS_NEW:
                return (
                    <StatusNew message={this.props.message} />
                );
            case Status.STATUS_OK:
                return (
                    <StatusOk message={this.props.message} />
                );
        }

        return (
            <StatusEmpty />
        );
    }
}

class StatusEmpty extends React.Component {
    render() {
        return null;
    }
}

class StatusLoading extends React.Component {
    render() {
        return (
            <span className="icon-text">
                <span className="icon-text__icon">
                    <Loading/>
                </span>
                <span className="icon-text__text">{this.props.message}</span>
            </span>
        );
    }
}

class StatusOk extends React.Component {
    render() {
        return (
            <span className="icon-text icon-text--ok">
                <span className="icon-text__icon"><svg
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    xmlnsXlink="http://www.w3.org/1999/xlink">
                    <use xlinkHref="#icon-ok" />
                </svg></span>
                <span className="icon-text__text">{this.props.message}</span>
            </span>
        );
    }
}

class StatusNew extends React.Component {
    render() {
        return (
            <span className="icon-text icon-text--info">
                <span className="icon-text__icon"><svg
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    xmlnsXlink="http://www.w3.org/1999/xlink">
                    <use xlinkHref="#icon-add" />
                </svg></span>
                <span className="icon-text__text">{this.props.message}</span>
            </span>
        );
    }
}

class StatusError extends React.Component {
    render() {
        return (
            <span className="icon-text icon-text--error">
                <span className="icon-text__icon"><svg
                    viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg"
                    xmlnsXlink="http://www.w3.org/1999/xlink">
                    <use xlinkHref="#icon-close" />
                </svg></span>
                <span className="icon-text__text">{this.props.message}</span>
            </span>
        );
    }
}