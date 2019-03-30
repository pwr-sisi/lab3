const http = require('http'), url = require('url');

const hostname = 'localhost';
const port = 3000;

const server = http.createServer((req, res) => {
  var query = url.parse(req.url,true).query;
  if ('name' in query) {
    var name = query.name + " ";
  } else {
    var name = "";
  }
  res.statusCode = 200;
  res.setHeader('Content-Type', 'text/html');
  res.end('<h1>Greeting app</h1><p>Hello ' + name + 'from NodeApp!</p><p><a href="/index.html">Main page</a></p>');
});

server.listen(port, hostname, () => {
  console.log(`Server running at http://${hostname}:${port}/`);
});
