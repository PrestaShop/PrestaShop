/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
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
