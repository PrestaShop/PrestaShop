# PrestaShop Release Creation

## Prerequisite

This tool needs these system commands:

- git
- rm
- mv
- mkdir
- du
- cd
- php
- cut

## Install and use

To create a release:

```
php tools/build/CreateRelease.php --version="1.7.2.4"
```

Available options:
* --version: Desired release version of PrestaShop. Required.
* --no-installer: Do not put the installer in the release. Default: false.
* --no-zip: Do not zip the release directory. Default: false.
* --destination-dir: Path where the release will be stored. Default: tools/build/releases/prestashop_{version}.
* --help: Show help.

This will:

* Export project with git archive to a temp location
* Define constants (`_PS_MODE_DEV_` to false etc...)
* Concatenate all licence files into one unique in {project_root}/LICENCES
* Create somes folders (app/cache, app/logs...)
* Clean project files and directories
* Zip release if no --no-zip arg
* Add the installer if no --no-installer arg
* Move the generated release to {project_root}/tools/build/releases or another directory if --destination-dir arg provided
