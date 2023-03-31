# VueJS Pages

Here, you'll find the VueJS single app pages:
- Stock Management
- Translations

By default, it's in production. It means, these are the compiled JS files, located in `/admin-dev/themes/new-theme/public/` that are served to the browser.

It you need to work on it to add some features or to debug, you need to follow these steps:
- Edit your `/app/config/config.yml` to set `twig > globals > webpack_server` to `true`
- Clean your Symfony cache (`app:console cache:clear` in the project root folder)
- Go to `/admin-dev/themes/new-theme/`
- Run `npm ci`
- Run `npm run start-dev-server`

Then, you can edit the files and you'll see your modifications live.

When you're done, commit the files you've modified, run `npm run build` and then, commit the compiled assets in an independent commit.

Don't forget to reset the `webpack_server` parameter to `false`.
