/*
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

// Read upstream assets and resolve output paths relative to the repository.
const fs = require('node:fs');
const path = require('node:path');

// Use the minifiers already installed for the modern back-office theme.
const root = path.resolve(__dirname, '../..');
const theme = path.join(root, 'admin-dev/themes/new-theme');
const terser = require(require.resolve('terser', {paths: [theme]}));
const csso = require(require.resolve('csso', {paths: [theme]}));
const source = path.resolve(process.argv[2]);
const distribution = path.resolve(process.argv[3]);
const destination = path.join(root, 'js/jquery/ui');
const version = '1.13.3';

// Reject a mismatched source package before writing generated assets.
if (JSON.parse(fs.readFileSync(path.join(source, 'package.json'), 'utf8')).version !== version
  || !fs.readFileSync(path.join(distribution, 'jquery-ui.js'), 'utf8').includes(`v${version}`)) {
  throw new Error(`Both source and distribution must be jQuery UI ${version}.`);
}

/** Build the browser script files and stylesheets consumed by Media::getJqueryUIPath(). */
async function build() {
  // Collect the shared helpers in dependency order under the existing core filename.
  const helpers = [
    'version', 'data', 'disable-selection', 'focusable', 'form', 'form-reset-mixin', 'ie',
    'jquery-patch', 'keycode', 'labels', 'plugin', 'safe-active-element', 'safe-blur',
    'scroll-parent', 'tabbable', 'unique-id',
  ];
  await writeScript('jquery.ui.core.min.js', helpers.map((name) => `ui/${name}.js`));
  await writeScript('jquery.ui.widget.min.js', ['ui/version.js', 'ui/widget.js']);
  await writeScript('jquery.ui.position.min.js', ['ui/version.js', 'ui/position.js']);

  // Keep each widget separate so PHP can load its dependencies only when needed.
  for (const name of fs.readdirSync(path.join(source, 'ui/widgets'))) {
    await writeScript(`jquery.ui.${name.replace('.js', '.min.js')}`, [`ui/widgets/${name}`]);
  }

  // Supply Color with effects core and preserve both public effect filename conventions.
  await writeScript('jquery.effects.core.min.js', ['ui/version.js', 'ui/vendor/jquery-color/jquery.color.js', 'ui/effect.js']);
  fs.copyFileSync(path.join(destination, 'jquery.effects.core.min.js'), path.join(destination, 'jquery.ui.effect.min.js'));
  for (const name of fs.readdirSync(path.join(source, 'ui/effects'))) {
    const effect = name.replace('effect-', '').replace('.js', '');
    const files = effect === 'scale'
      ? ['ui/effects/effect-size.js', 'ui/effects/effect-scale.js', 'ui/effects/effect-puff.js']
      : [`ui/effects/${name}`];
    await writeScript(`jquery.effects.${effect}.min.js`, files);
    fs.copyFileSync(path.join(destination, `jquery.effects.${effect}.min.js`), path.join(destination, `jquery.ui.effect-${effect}.min.js`));
  }

  // Update the full distribution with the same browser-global loading as the separate files.
  const full = `(function(define, exports, module) {\n${fs.readFileSync(path.join(distribution, 'jquery-ui.js'), 'utf8')}\n}).call(this);\n`.replace(/[\t ]+$/gm, '');
  fs.writeFileSync(path.join(destination, 'jquery-ui.js'), full);
  fs.writeFileSync(path.join(destination, 'jquery-ui.min.js'), (await terser.minify(full, {format: {comments: /^!/}})).code + '\n');

  // Preserve PrestaShop stylesheet names and rewrite their relative imports.
  const cssDestination = path.join(destination, 'themes/base');
  for (const name of fs.readdirSync(path.join(source, 'themes/base')).filter((file) => file.endsWith('.css'))) {
    let css = fs.readFileSync(path.join(source, 'themes/base', name), 'utf8');

    // These widgets use button styles without requiring the button JavaScript widget.
    if (name === 'checkboxradio.css' || name === 'selectmenu.css') {
      css = fs.readFileSync(path.join(source, 'themes/base/button.css'), 'utf8') + css;
    }

    // Match relative imports to the filenames in each output directory.
    css = css.replace(/(["'])([\w-]+)\.css\1/g, '$1jquery.ui.$2.css$1');
    fs.writeFileSync(path.join(cssDestination, `jquery.ui.${name}`), css.trimEnd() + '\n');
    const minifiedCss = css.replace(/(["'])(jquery\.ui\.[\w-]+)\.css\1/g, '$1$2.min.css$1');
    fs.writeFileSync(path.join(cssDestination, 'minified', `jquery.ui.${name.replace('.css', '.min.css')}`), csso.minify(minifiedCss).css + '\n');
  }

  // Copy the matching complete theme and its image assets for both CSS locations.
  fs.copyFileSync(path.join(distribution, 'jquery-ui.css'), path.join(cssDestination, 'jquery-ui.css'));
  fs.copyFileSync(path.join(distribution, 'jquery-ui.min.css'), path.join(cssDestination, 'minified/jquery-ui.min.css'));
  for (const name of fs.readdirSync(path.join(source, 'themes/base/images'))) {
    fs.copyFileSync(path.join(source, 'themes/base/images', name), path.join(cssDestination, 'images', name));
    fs.copyFileSync(path.join(source, 'themes/base/images', name), path.join(cssDestination, 'minified/images', name));
  }

  // Refresh upstream locales while retaining the additional PrestaShop locale aliases.
  const locales = fs.readdirSync(path.join(source, 'ui/i18n')).sort();
  for (const name of locales) {
    const locale = fs.readFileSync(path.join(source, 'ui/i18n', name), 'utf8');
    fs.writeFileSync(path.join(destination, 'i18n', `jquery.ui.${name}`), `(function(define) {\n${locale}\n}).call(this);\n`);
  }

  // Retain the combined locale entry point and the upstream license.
  fs.writeFileSync(path.join(destination, 'i18n/jquery-ui-i18n.js'), locales.map((name) => fs.readFileSync(path.join(destination, 'i18n', `jquery.ui.${name}`), 'utf8')).join('\n'));
  fs.copyFileSync(path.join(source, 'LICENSE.txt'), path.join(destination, 'LICENSE.txt'));
}

/** Minify upstream modules without removing their license notices. */
async function writeScript(name, files) {
  // Concatenate only the source modules belonging to this public entry point.
  const code = files.map((file) => fs.readFileSync(path.join(source, file), 'utf8')).join('\n');

  // PHP loads browser globals, including on pages which also expose an AMD loader.
  const result = await terser.minify(`(function(define, exports, module) {\n${code}\n}).call(this);`, {format: {comments: /^!/}});
  fs.writeFileSync(path.join(destination, name), result.code + '\n');
}

// Surface build failures to the invoking shell.
build().catch((error) => {
  console.error(error);
  process.exitCode = 1;
});
