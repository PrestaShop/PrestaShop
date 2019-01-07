fs = require("fs");
let gulp = require('gulp'),
  replace = require('gulp-replace'),
  rename = require("gulp-rename");

gulp.task('default', function () {

  let fileContentJs = fs.readFileSync("../mochawesome-report/assets/app.js", "utf8");
  let fileContentCss = fs.readFileSync("../mochawesome-report/assets/app.css", "utf8");

  gulp.src(['../mochawesome-report/mochawesome.html'])
    .pipe(replace('<script src="assets/app.js"></script>', '<script type="text/javascript">' + fileContentJs + '</script>'))
    .pipe(replace('<link rel="stylesheet" href="assets/app.css"/>', '<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"><style>' + fileContentCss + '</style>'))
    .pipe(rename("test_report.html"))
    .pipe(gulp.dest(""));
});
