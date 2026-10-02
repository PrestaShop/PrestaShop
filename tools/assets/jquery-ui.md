# jQuery UI assets

The assets in `js/jquery/ui` use jQuery UI **1.13.3** and are consumed by the PHP
component loader. The modern admin independently uses its existing
`jquery-ui-dist` **1.13.3** dependency and imports; this build does not change them.
Keep these versions aligned when updating either distribution.

## Rebuilding

Install the modern admin's dependencies using its usual build setup first.
The generator uses its installed `terser` and `csso` minifiers.

Download and extract these two official, versioned archives into a temporary directory:

- [Source modules (npm jquery-ui)](https://registry.npmjs.org/jquery-ui/-/jquery-ui-1.13.3.tgz)
- [Full distribution](https://jqueryui.com/resources/download/jquery-ui-1.13.3.zip)

From the repository root, run:

```sh
node tools/assets/build-jquery-ui.cjs /path/to/package /path/to/jquery-ui-1.13.3
```

Commit the generated assets together with any changes to the generator and
`Media::$jquery_ui_dependencies`. These generated assets do not require a
modern admin rebuild.

## Loading conventions

- Existing `addJqueryUI()` component names and `jquery.ui.*` filenames remain available.
- `jquery.ui.core.min.js` contains the shared upstream helpers in dependency order.
  Upstream's `ui/core.js` is an AMD dependency list, not a browser-global bundle.
- Widgets remain separate. Button's older API requires the separate controlgroup and
  checkboxradio widgets. The PHP map includes them for button, dialog and spinner.
- The map contains **complete ordered dependencies**, because `getJqueryUIPath()`
  deliberately loads each dependency with recursive dependency checks disabled.
- Dialog's draggable and resizable enhancements remain optional, as in the existing
  PHP loader. Request those interactions explicitly when needed.
- Effects core includes jQuery Color. Both `jquery.effects.*` and `jquery.ui.effect*`
  filenames are generated. Scale continues to include size and puff, matching its
  existing entry point.
- The files explicitly use browser globals even when an AMD loader is present.
- Upstream's default backward compatibility remains enabled, including the older
  button and buttonset APIs. An explicit `jQuery.uiBackCompat = false` is respected.
  This flag does not restore APIs removed by upstream.
- Component CSS, full CSS, theme images and upstream datepicker translations are
  generated together. Additional PrestaShop locale files absent from upstream are
  retained. Checkboxradio and selectmenu include the button structure CSS they need.

The full distribution in `js/jquery/ui` comes directly from the matching official
download. It is independent of the modern admin's npm build.
