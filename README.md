About PrestaShop
--------

[![PHP checks and unit tests](https://github.com/PrestaShop/PrestaShop/actions/workflows/php.yml/badge.svg)](https://github.com/PrestaShop/PrestaShop/actions/workflows/php.yml)
[![Integration tests](https://github.com/PrestaShop/PrestaShop/actions/workflows/integration.yml/badge.svg)](https://github.com/PrestaShop/PrestaShop/actions/workflows/integration.yml)
[![UI tests](https://github.com/PrestaShop/PrestaShop/actions/workflows/sanity.yml/badge.svg)](https://github.com/PrestaShop/PrestaShop/actions/workflows/sanity.yml)
[![Nightly Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fapi-nightly.prestashop-project.org%2Fdata%2Fbadge&label=Nightly%20Status&cacheSeconds=3600)](https://nightly.prestashop-project.org/)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg?style=flat-square)](https://php.net/)
[![GitHub release](https://img.shields.io/github/v/release/prestashop/prestashop)](https://github.com/PrestaShop/PrestaShop)
[![Slack chat](https://img.shields.io/badge/Chat-on%20Slack-red)](https://www.prestashop-project.org/slack/)
[![GitHub forks](https://img.shields.io/github/forks/PrestaShop/PrestaShop)](https://github.com/PrestaShop/PrestaShop/network)
[![GitHub stars](https://img.shields.io/github/stars/PrestaShop/PrestaShop)](https://github.com/PrestaShop/PrestaShop/stargazers)

PrestaShop is an Open Source e-commerce web application, committed to providing the best shopping cart experience for both merchants and customers. It is written in PHP, is highly customizable, supports all the major payment services, is translated in many languages and localized for many countries, has a fully responsive design (both front and back office), etc. [See all the available features][available-features].

<p align="center">
  <img src="https://user-images.githubusercontent.com/2137763/201319765-9157f702-4970-4258-8390-1187de2ad587.png" alt="PrestaShop 8.0 back office"/>
</p>

This repository contains the source code of PrestaShop, which is intended for development and preview only. To download the latest stable public version of PrestaShop (currently, version 8.0), please go to [the releases page][download].


About the 'develop' branch
--------

The 'develop' branch of this repository contains the work in progress source code for the next version of PrestaShop.

For more information on our branch system, read our guide on [installing PrestaShop for development][install-guide-dev].

The first stable version of PrestaShop 8.0, was released on October 26th, 2022. Further updates have been released since then. Learn more about it on [the Build devblog](https://build.prestashop-project.org/tag/8.0/).

Server configuration
--------

To install the latest PrestaShop 9.0, you need a web server running PHP 8.1+ and any flavor of MySQL 5.6+ (MySQL, MariaDB, Percona Server, etc.).

You will also need a database administration tool, such as phpMyAdmin, in order to create a database for PrestaShop.
We recommend the Apache or Nginx web servers (check out our [example Nginx configuration file][example-nginx]).

You can find more information on our [System requirements][system-requirements] page and on the [System Administrator Guide][sysadmin-guide].

Installation
--------

If you downloaded the source code from GitHub, read our guide on [installing PrestaShop for development][install-guide-dev]. If you intend to install a production shop, make sure to download the latest version from [our releases page][download], then read the [install guide for users][install-guide].

Docker compose
--------

PrestaShop can also be deployed with Docker and its tool [Docker compose][docker-compose].

To run the software, use:

```
docker compose up
```

Then reach your shop on this URL: http://localhost:8001

Docker will bind your port 8001 to the web server. If you want to use other port, open and modify the file `docker-compose.yml`.
MySQL credentials can also be found and modified in this file if needed.

**Note:**  Before auto-installing PrestaShop, this container checks the file *config/settings.inc.php* does not exist on startup.
If you expect the container to (re)install your shop, remove this file if it exists. And make sure the container user `www-data`
has write access to the whole workspace.

To fully reset your repo and get a fresh start, run (be careful: this removes all your extra files):

```
# clean everything that is not part of the original repository (node_modules, etc.)
git fetch origin
git reset --hard origin/develop
git clean -dfx

# inform build scripts to reinstall shop
rm config/settings.inc.php

# clear all docker caches and rebuild everything
docker compose down -v
docker compose build --no-cache
docker compose up --build --force-recreate
```
**Note:** To add a PHPMyAdmin service to your Docker Compose setup, you can copy the existing `docker-compose.override.yml.dist` to `docker-compose.override.yml` and then start your Docker Compose setup (override file will be included as it is a convention)

```
cp docker-compose.override.yml.dist docker-compose.override.yml
docker compose up
```

Documentation
--------

For technical information (core, module and theme development, performance...), head on to [PrestaShop DevDocs][devdocs]

If you want to learn how to use PrestaShop 8, read our [User documentation][user-doc].

First-time users will be particularly interested in the following guides:

* [Getting Started][getting-started]: How to install PrestaShop, and what you need to know.
* [User Guide][user-guide]: All there is to know to put PrestaShop to good use.
* [Updating Guide][updating-guide]: Switching to the newest version is not trivial. Make sure you do it right.
* [Merchant's Guide][merchant-guide]: Tips and tricks for first-time online sellers.
* The [FAQ][faq-17] page should also be of tremendous help to you.


Contributing
--------

PrestaShop is an Open Source project, and it wouldn't be possible without the help of the [hundreds of contributors][contributors-md], who submitted improvements and bugfixes over the years. Thank you all!

If you want to contribute code to PrestaShop, read the [CONTRIBUTING.md][contributing-md] file in this repository or read the [tutorials about contribution][contributing-tutorial] on the documentation site.

Don't know where to start? Check the [good first issue](https://github.com/PrestaShop/PrestaShop/issues?q=is%3Aissue+is%3Aopen+label%3A%22good+first+issue%22) label to have a look at all beginner-friendly improvements and bug fixes.

If you want to help translate PrestaShop in your language, [join us on Crowdin][crowdin]!


Reporting Issues
--------

Our bugtracker is on GitHub. We encourage you to [create detailed issues][create-issue] as soon as you see them.

Read our [Contribute by reporting issues guide][reporting-issues] for details and tips.


Reporting Security Issues
--------

Responsible (and private) disclosure is a standard practice when someone encounters a security problem: before making it public, the discoverer informs the Core team about it, so that a fix can be prepared, and thus minimize the potential damage.

The PrestaShop team tries to be very proactive when preventing security problems. Even so, critical issues might surface without notice.

This is why we have set up a [Bug Bounty Program][bug-bounty] where anyone can privately contact us with all the details about issues that affect the security of PrestaShop merchants or customers. Our security team will answer you, and discuss of a timeframe for your publication of the details.

Understanding a security issue means knowing how the attacker got in and hacked the site. If you have those details, then please do contact us privately about it (and please do not publish those details before we answer). If you do not know how the attacker got in, please [ask for help][support].


Extending PrestaShop
--------

PrestaShop is a very extensible e-commerce platform, both through modules and themes. Developers can even override the default components and behaviors. Learn more about this on the [Modules documentation][modules-devdocs] and the [Themes documentation][themes-devdocs].


Community forums
--------

You can discuss about e-commerce, help other merchants and get help, and contribute to improving PrestaShop together with the PrestaShop community on [PrestaShop Slack channel][chat], [project's discussions on GitHub][ghdiscussions] or on the [the PrestaShop forums][forums].

Thank you for downloading and using the PrestaShop Open Source e-commerce solution!

[available-features]: https://www.prestashop.com/en/online-store-builder
[download]: https://github.com/PrestaShop/PrestaShop/releases
[forums]: https://www.prestashop.com/forums/
[ghdiscussions]: https://github.com/PrestaShop/PrestaShop/discussions
[support]: https://www.prestashop-project.org/support/
[chat]: https://www.prestashop-project.org/slack/
[user-doc]: https://docs.prestashop-project.org
[contributing-md]: CONTRIBUTING.md
[contributing-tutorial]: https://devdocs.prestashop-project.org/8/contribute/
[crowdin]: https://crowdin.net/project/prestashop-official
[getting-started]: https://docs.prestashop-project.org/v.8-documentation/v/english/getting-started
[user-guide]: https://docs.prestashop-project.org/v.8-documentation/v/english/user-guide
[updating-guide]: https://docs.prestashop-project.org/1-6-documentation/english-documentation/updating-prestashop
[merchant-guide]: https://docs.prestashop-project.org/1-6-documentation/english-documentation/merchants-guide
[faq-17]: https://devdocs.prestashop-project.org/8/faq/
[sysadmin-guide]: https://docs.prestashop-project.org/1-6-documentation/english-documentation/system-administrator-guide
[contributors-md]: CONTRIBUTORS.md
[example-nginx]: https://devdocs.prestashop-project.org/8/basics/installation/nginx/
[docker-compose]: https://docs.docker.com/compose/
[install-guide-dev]: https://devdocs.prestashop-project.org/8/basics/installation/
[system-requirements]: https://devdocs.prestashop-project.org/8/basics/installation/system-requirements/
[install-guide]: https://docs.prestashop-project.org/v.8-documentation/v/english/getting-started/installing-prestashop
[devdocs]: https://devdocs.prestashop-project.org/
[create-issue]: https://github.com/PrestaShop/PrestaShop/issues/new/choose
[reporting-issues]: https://devdocs.prestashop-project.org/8/contribute/contribute-reporting-issues/
[modules-devdocs]: https://devdocs.prestashop-project.org/8/modules/
[themes-devdocs]: https://devdocs.prestashop-project.org/8/themes/
[bug-bounty]: https://www.prestashop-project.org/security/bug-bounty/
