var fs = require('fs'),
    http = require('http');

http.createServer(function (req, res) {
    if (req.url === '/') {
        res.write('Welcome home!');
        res.end();
    } else if (req.url.startsWith('/redirect')) {
        res.writeHead(301, { 'Location': req.url.replace('/redirect', '/fixtures') });
        res.end();
    } else if (req.url.startsWith('/fixtures')) {
        fs.readFile(__dirname + req.url, function (err, data) {
            if (err) {
                res.writeHead(404);
                res.end(JSON.stringify(err));
                return;
            }
            res.writeHead(200);
            res.end(data);
        });
    } else if (req.url.startsWith('/private')) {
        if (req.headers.authorization === `Basic ${btoa('client:secret')}`) {
            res.writeHead(301, { 'Location': req.url.replace('/private', '/fixtures') });
            res.end();
        } else {
            res.writeHead(403);
            res.end();
        }
    } else {
        res.writeHead(404);
        res.end();
    }
}).listen(8888);
