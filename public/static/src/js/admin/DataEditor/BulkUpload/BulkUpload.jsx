import React from 'react';
import FileDrop from '../../Utils/FileDrop';
import Loading from '../../Utils/Loading';
import Message from '../../Utils/Message';

export default class BulkUpload extends React.Component {
    static get STATUS_UPLOADING() { return 'uploading'; }
    static get STATUS_PROCESSING() { return 'processing'; }
    static get STATUS_ERROR() { return 'error'; }
    constructor() {
        super();
        this.state = {
            status: null,
            results : []
        };
        this.loop = false;
    }

    componentDidMount() {
        this.setState({
            stats : this.props.bulkStats || null
        });
    }

    handleReceievedFile(file) {
        this.setState({
            status : BulkUpload.STATUS_UPLOADING
        });

        let reader = new FileReader(),
            url = '/admin/data/bulk-upload.json';

        reader.onload = function( e ) {

            fetch(url, {
                method: 'post',
                body: e.currentTarget.result,
                credentials: 'same-origin'
            })
                .then(function(response) {
                    return response.json();
                }.bind(this))
                .then(function(data) {
                    this.setState({
                        stats : data.stats,
                        status : null
                    });
                }.bind(this))
                .catch( function(error) {
                    this.setState({
                        status : BulkUpload.STATUS_ERROR
                    });
                }.bind(this));

        }.bind(this);

        reader.readAsText( file );
    }

    handleClickBatch() {
        this.setState({
            status : BulkUpload.STATUS_PROCESSING
        });

        let url = '/admin/data/bulk-process.json';
        fetch(url, {
            method: 'post',
            credentials: 'same-origin'
        })
            .then(function(response) {
                return response.json();
            }.bind(this))
            .then(function(data) {
                let results = data.securities.reverse();
                results = results.concat(this.state.results);

                this.setState({
                    stats : data.stats,
                    results : results.slice(0,500)
                });

                if (this.loop) {
                    return this.handleClickBatch();
                }

                this.setState({
                    status : null
                });
            }.bind(this))
            .catch( function(e) {
                this.setState({
                    status : BulkUpload.STATUS_ERROR
                });
            }.bind(this));
    }

    handleClickAll() {
        this.loop = true;
        this.handleClickBatch();
    }

    handleStop() {
        this.loop = false;
    }

    render() {
        let filepanel = (
            <FileDrop onFileRecieved={this.handleReceievedFile.bind(this)} />
        ),
            panel = null;

        if (this.state.status == BulkUpload.STATUS_UPLOADING) {
            panel = (<Loading />);
            filepanel = null;
        }

        if (this.state.stats) {
            let processButtons = (
                <span>
                    <button className="button button--fat"
                            onClick={this.handleClickBatch.bind(this)}
                        >Process Batch</button>
                    {' '}
                    <button className="button button--fat"
                            onClick={this.handleClickAll.bind(this)}
                        >Process All</button>
                </span>
            ),
                complete = this.state.stats.totalProcessed == this.state.totalToProcess;

            if (complete) {
                processButtons = null;
            } else {
                filepanel = null;
            }

            if (this.state.status == BulkUpload.STATUS_PROCESSING) {
                processButtons = [(<Loading key="processloading" cssClasses="loading--sibling" />)];
                if (this.loop) {
                    processButtons.push((
                        <button key="stopbutton" className="button button--fat"
                                onClick={this.handleStop.bind(this)}
                            >Stop</button>
                    ));
                }
            }

            panel = (
                <div className="panel g-unit">
                    <div className="grid grid--flat">
                        <div className="g 2/3 g--align-center">
                            <p className="a">
                                {this.state.stats.totalProcessedFormatted}
                                /
                                {this.state.stats.totalToProcessFormatted} processed
                            </p>
                        </div>
                        <div className="g 1/3 g--align-center">
                            <div className="text--right">
                                {processButtons}
                            </div>
                        </div>
                    </div>
                </div>
            );
        }

        let message = null;
        if (this.state.status == BulkUpload.STATUS_ERROR) {
            message =(<Message type={Message.TYPE_ERROR} message="An error occurred" />);
        }

        let results = null,
            items = [];
        if (this.state.results.length > 0) {
            this.state.results.forEach(function(item) {
                items.push(
                    <BulkUploadSecurity key={item.isin} data={item} />
                );
            });
            results = (
                <table className="table table--striped">
                    <thead>
                        <tr>
                            <th>ISIN</th>
                            <th>Name</th>
                            <th>Start Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        {items}
                    </tbody>
                </table>
            )
        }

        return (
            <div>
                <h1 className="b g-unit">Bulk upload ISINs</h1>
                {filepanel}
                {panel}
                {message}
                {results}
            </div>
        );
    };
}

class BulkUploadSecurity extends React.Component {
    render() {
        let url = '/securities/' + this.props.data.isin;
        return (
            <tr>
                <td><a href={url}>{this.props.data.isin}</a></td>
                <td>{this.props.data.name}</td>
                <td>{this.props.data.startDate}</td>
            </tr>
        )
    }
}