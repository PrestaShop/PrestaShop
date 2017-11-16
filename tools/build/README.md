# PrestaShop Release Creation

To create a release:

```
php CreateRelease.php --version="1.7.2.4"
```
This will:
            
* Define constants (`_PS_MODE_DEV_` to false etc...)
* Concatenate all licence files into one unique in /LICENCES
* Update composer.json
* Create somes folders (app/cache, app/logs...)
* Create packages for all langs (only fr currently).

Created releases are available in tools/build/releases directory.