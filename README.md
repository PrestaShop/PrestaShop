About PrestaShop
--------

[![PHP checks and unit tests](https://github.com/PrestaShop/PrestaShop/actions/workflows/php.yml/badge.svg)](https://github.com/PrestaShop/PrestaShop/actions/workflows/php.yml)
[![Integration tests](https://github.com/PrestaShop/PrestaShop/actions/workflows/integration.yml/badge.svg)](https://github.com/PrestaShop/PrestaShop/actions/workflows/integration.yml)
[![UI tests](https://github.com/PrestaShop/PrestaShop/actions/workflows/sanity.yml/badge.svg)](https://github.com/PrestaShop/PrestaShop/actions/workflows/sanity.yml)
[![Nightly Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fapi-nightly.prestashop-project.org%2Fdata%2Fbadge&label=Nightly%20Status&cacheSeconds=3600)](https://nightly.prestashop-project.org/)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg?style=flat-square)](https://php.net/)
[![GitHub release](https://img.shields.io/github/v/release/prestashop/prestashop?sort=semver)](https://github.com/PrestaShop/PrestaShop)
[![LFX Health Score](https://insights.linuxfoundation.org/api/badge/health-score?project=prestashop)](https://insights.linuxfoundation.org/project/prestashop)
[![Slack chat](https://img.shields.io/badge/Chat-on%20Slack-red)](https://www.prestashop-project.org/slack/)
[![GitHub forks](https://img.shields.io/github/forks/PrestaShop/PrestaShop)](https://github.com/PrestaShop/PrestaShop/network)
[![GitHub stars](https://img.shields.io/github/stars/PrestaShop/PrestaShop)](https://github.com/PrestaShop/PrestaShop/stargazers)

PrestaShop is an Open Source e-commerce web application, committed to providing the best shopping cart experience for both merchants and customers. It is written in PHP, is highly customizable, supports all the major payment services, is translated in many languages and localized for many countries, has a fully responsive design (both front and back office), etc. [See all the available features][available-features].

<p align="center">
  <img src="https://github.com/user-attachments/assets/e6342778-e528-4ae7-acf2-f0d097a1e932" alt="PrestaShop 9.0 back office"/>
</p>

This repository contains the source code of PrestaShop, which is intended for development and preview only. To download the latest stable public version of PrestaShop (currently, version 9.0), please go to [the releases page][download].

The first stable version of PrestaShop 9.0 was released on June 10th, 2025. Learn more about it on [the Build devblog](https://build.prestashop-project.org/tag/9.0/).

About the `develop` branch
--------

The `develop` branch of this repository contains the work in progress source code for the next version of PrestaShop. Currently, it is exclusively for version 9.1.

For more information on our branch system, read our guide on [installing PrestaShop for development][install-guide-dev].

Server configuration
--------

To install the latest PrestaShop 9.0, you need a web server running PHP 8.1+ and any flavor of MySQL 5.6+ (MySQL, MariaDB, Percona Server, etc.).

You will also need a database administration tool, such as phpMyAdmin, in order to create a database for PrestaShop.
We recommend the Apache or Nginx web servers (check out our [example Nginx configuration file][example-nginx]).

You can find more information on our [System requirements][system-requirements] page and on the [System Administrator Guide][sysadmin-guide].

Installation
--------

If you downloaded the source code from GitHub, read our guide on [installing PrestaShop for development][install-guide-dev]. If you intend to install a production shop, make sure to download the latest version from [our releases page][download], then read the [install guide for users][install-guide].

## 🐳 Docker Development Environment

PrestaShop provides a complete Docker-based development environment.

### Quick Start

```bash
# Start the development environment
make docker-start

# Access your PrestaShop installation
# Frontend: http://localhost:8001
# Backend: http://localhost:8001/admin-dev
# Email testing: http://localhost:1080
```

**Default Admin Credentials:**
- Email: `demo@prestashop.com`
- Password: `Correct Horse Battery Staple`

Or the backoffice on this URL: http://localhost:8001/admin-dev (default access credentials: demo@prestashop.com / Correct Horse Battery Staple)

You can customize the admin credentials by setting the following environment variables before running docker compose:
```
export ADMIN_MAIL=your-email@example.com
export ADMIN_PASSWD=Your-Secure-Password
docker compose up
```

Docker will bind your port **8001** to the web server. If you want to use other port, open and modify the file `docker-compose.yml`.
MySQL credentials can also be found and modified in this file if needed.

**Note:**  Before auto-installing PrestaShop, this container checks the file *app/config/parameters.php* does not exist on startup.
If you expect the container to (re)install your shop, remove this file if it exists. And make sure the container user `www-data`
has write access to the whole workspace.

### Documentation

For detailed information, see:

- **[🚀 Development Guide](./docs/DEVELOPMENT.md)** - Complete setup and development guide

Documentation
--------

For technical information (core, module and theme development, performance...), head on to [PrestaShop DevDocs][devdocs]

If you want to learn how to use PrestaShop 9, read our [User documentation][user-doc].

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

[available-features]: https://prestashop.com/create-online-store/
[download]: https://www.prestashop-project.org/releases/prestashop90/
[forums]: https://www.prestashop.com/forums/
[ghdiscussions]: https://github.com/PrestaShop/PrestaShop/discussions
[support]: https://www.prestashop-project.org/support/
[chat]: https://www.prestashop-project.org/slack/
[user-doc]: https://docs.prestashop-project.org
[contributing-md]: CONTRIBUTING.md
[contributing-tutorial]: https://devdocs.prestashop-project.org/9/contribute/
[crowdin]: https://crowdin.net/project/prestashop-official
[getting-started]: https://docs.prestashop-project.org/v.8-documentation/v/english/getting-started
[user-guide]: https://docs.prestashop-project.org/v.8-documentation/v/english/user-guide
[updating-guide]: https://docs.prestashop-project.org/1-6-documentation/english-documentation/updating-prestashop
[merchant-guide]: https://docs.prestashop-project.org/1-6-documentation/english-documentation/merchants-guide
[faq-17]: https://devdocs.prestashop-project.org/9/faq/
[sysadmin-guide]: https://docs.prestashop-project.org/1-6-documentation/english-documentation/system-administrator-guide
[contributors-md]: CONTRIBUTORS.md
[example-nginx]: https://devdocs.prestashop-project.org/9/basics/installation/nginx/
[docker-compose]: https://docs.docker.com/compose/
[install-guide-dev]: https://devdocs.prestashop-project.org/9/basics/installation/
[system-requirements]: https://devdocs.prestashop-project.org/9/basics/installation/system-requirements/
[install-guide]: https://docs.prestashop-project.org/v.8-documentation/v/english/getting-started/installing-prestashop
[devdocs]: https://devdocs.prestashop-project.org/
[create-issue]: https://github.com/PrestaShop/PrestaShop/issues/new/choose
[reporting-issues]: https://devdocs.prestashop-project.org/9/contribute/contribute-reporting-issues/
[modules-devdocs]: https://devdocs.prestashop-project.org/9/modules/
[themes-devdocs]: https://devdocs.prestashop-project.org/9/themes/
[bug-bounty]: https://www.prestashop-project.org/security/bug-bounty/
