export default class AutoComplete {
    constructor(path, callback) {
        this.path = path;
        this.callback = callback;
        this.timer = null;
        this.timeoutValue = 600;
    }

    newValue(value) {
        clearTimeout(this.timer);
        this.timer = setTimeout(function () {
            let url = this.path.replace('%s', value),
                request = new XMLHttpRequest(),
                callback = this.callback;
            request.open('GET', url, true);

            request.onload = function() {
                if (this.status >= 200 && this.status < 400) {
                    var data = JSON.parse(this.response);
                    return callback(data);
                } else {
                    return callback(false);
                }
            };

            request.onerror = function() {
                return callback(false);
            };

            request.send();
        }.bind(this), this.timeoutValue);
    }
}