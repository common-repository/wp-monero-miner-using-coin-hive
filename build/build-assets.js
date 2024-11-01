var minifier = require('minifier');
var path = require('path');

var files = [
    './admin/css/wp-coin-hive-admin.css',
    './includes/css/wp-coin-hive-login.css',
    './includes/css/wp-coin-hive-redirect.css',
    './public/css/wp-coin-hive-public.css',
    './includes/js/wp-coin-hive.js',
    './includes/js/wp-monero-miner-class.js',
    './includes/js/wp-coin-hive-util.js'
];

for (var i = 0; i < files.length; i++) {
    console.log(path.basename(files[i]));
    minifier.minify(files[i]);
}

console.log('SUCCESS');