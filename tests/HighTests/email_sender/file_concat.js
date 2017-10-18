var fs = require('fs');
var i = 0;
var version = new Array();
version = [1.6, 1.7];

var workSpace = process.env.TRAVIS_BUILD_DIR

while (i < 2) {

// Verify the existence of Mocha Reporter
    if (fs.existsSync(workSpace + '/test/itg/' + version[i] + '/mochawesome-report')) {
        var cssFile = '';
        var jsFile = '';
        var htmlFile = '';

        // concatenate css and js files in a single html file
        function processFile() {
            jsFile = "<script type='text/javascript'>" + jsFile + "</script></body>";
            cssFile = "<style type='text/css'>" + cssFile + "</style>";

            htmlFile = htmlFile.replace('<link rel="stylesheet" href="assets/app.css"/>', '<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">' + cssFile);
            htmlFile = htmlFile.replace('</body>', jsFile);
            fs.writeFile("email_sender/test_report_" + version[i] + ".html", htmlFile, function (err) {

                if (err) {
                    return console.log(err);
                }

                console.log("The file was saved!");
            });

        }

        // read content of Js file
        jsFile = fs.readFileSync(workSpace + '/test/itg/' + version[i] + '/mochawesome-report/assets/app.js').toString();

        // read content of CSS file
        cssFile = fs.readFileSync(workSpace + '/test/itg/' + version[i] + '/mochawesome-report/assets/app.css').toString();

        // read content of Html file
        htmlFile = fs.readFileSync(workSpace + '/test/itg/' + version[i] + '/mochawesome-report/mochawesome.html').toString();

        processFile();

        i++;
    } else {
        i++
    }

}


