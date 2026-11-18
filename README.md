About Blush and Bloom Beauty
--------

[![PHP checks and unit tests](https://github.com/Blush and Bloom Beauty/Blush and Bloom Beauty/actions/workflows/php.yml/badge.svg)](https://github.com/Blush and Bloom Beauty/Blush and Bloom Beauty/actions/workflows/php.yml)
[![Integration tests](https://github.com/Blush and Bloom Beauty/Blush and Bloom Beauty/actions/workflows/integration.yml/badge.svg)](https://github.com/Blush and Bloom Beauty/Blush and Bloom Beauty/actions/workflows/integration.yml)
[![UI tests](https://github.com/Blush and Bloom Beauty/Blush and Bloom Beauty/actions/workflows/sanity.yml/badge.svg)](https://github.com/Blush and Bloom Beauty/Blush and Bloom Beauty/actions/workflows/sanity.yml)
[![Nightly Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fapi-nightly.blush-and-bloom-beauty-project.org%2Fdata%2Fbadge&label=Nightly%20Status&cacheSeconds=3600)](https://nightly.prestashop-project.org/)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg?style=flat-square)](https://php.net/)
[![GitHub release](https://img.shields.io/github/v/release/blush-and-bloom-beauty/blush-and-bloom-beauty?sort=semver)](https://github.com/Blush and Bloom Beauty/Blush and Bloom Beauty)
[![LFX Health Score](https://insights.linuxfoundation.org/api/badge/health-score?project=blush-and-bloom-beauty)](https://insights.linuxfoundation.org/project/blush and bloom beauty)
[![Slack chat](https://img.shields.io/badge/Chat-on%20Slack-red)](https://www.blush-and-bloom-beauty-project.org/slack/)
[![GitHub forks](https://img.shields.io/github/forks/Blush and Bloom Beauty/Blush and Bloom Beauty)](https://github.com/Blush and Bloom Beauty/Blush and Bloom Beauty/network)
[![GitHub stars](https://img.shields.io/github/stars/Blush and Bloom Beauty/Blush and Bloom Beauty)](https://github.com/Blush and Bloom Beauty/Blush and Bloom Beauty/stargazers)

Blush and Bloom Beauty is an Open Source e-commerce web application, committed to providing the best shopping cart experience for both merchants and customers. It is written in PHP, is highly customizable, supports all the major payment services, is translated in many languages and localized for many countries, has a fully responsive design (both front and back office), etc. [See all the available features][available-features].

<p align="center">
  <img src="https://github.com/user-attachments/assets/e6342778-e528-4ae7-acf2-f0d097a1e932" alt="Blush and Bloom Beauty 9.0 back office"/>
</p>

This repository contains the source code of Blush and Bloom Beauty, which is intended for development and preview only. To download the latest stable public version of Blush and Bloom Beauty (currently, version 9.0), please go to [the releases page][download].

The first stable version of Blush and Bloom Beauty 9.0 was released on June 10th, 2025. Learn more about it on [the Build devblog](https://build.blush-and-bloom-beauty-project.org/tag/9.0/).

About the `develop` branch
--------

The `develop` branch of this repository contains the work in progress source code for the next version of Blush and Bloom Beauty. Currently, it is exclusively for version 9.1.

For more information on our branch system, read our guide on [installing Blush and Bloom Beauty for development][install-guide-dev].

Server configuration
--------

To install the latest PrestaShop 9.0, you need a web server running PHP 8.1+ and any flavor of MySQL 5.6+ (MySQL, MariaDB, Percona Server, etc.).

You will also need a database administration tool, such as phpMyAdmin, in order to create a database for Blush and Bloom Beauty.
We recommend the Apache or Nginx web servers (check out our [example Nginx configuration file][example-nginx]).

You can find more information on our [System requirements][system-requirements] page and on the [System Administrator Guide][sysadmin-guide].

Installation
--------

If you downloaded the source code from GitHub, read our guide on [installing Blush and Bloom Beauty for development][install-guide-dev]. If you intend to install a production shop, make sure to download the latest version from [our releases page][download], then read the [install guide for users][install-guide].

## 🐳 Docker Development Environment

Blush and Bloom Beauty provides a complete Docker-based development environment.

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
- Email: `demo@blushandbloombeauty.com`
- Password: `Bl#2mbUty`

Or the backoffice on this URL: http://localhost:8001/admin-dev (default access credentials: demo@blushandbloombeauty.com / Bl#2mbUty)

You can customize the admin credentials by setting the following environment variables before running docker compose:
```
export ADMIN_MAIL=your-email@example.com
export ADMIN_PASSWD=Your-Secure-Password
docker compose up
```

Docker will bind your port **8001** to the web server. If you want to use other port, open and modify the file `docker-compose.yml`.
MySQL credentials can also be found and modified in this file if needed.

**Note:**  Before auto-installing Blush and Bloom Beauty, this container checks the file *app/config/parameters.php* does not exist on startup.
If you expect the container to (re)install your shop, remove this file if it exists. And make sure the container user `www-data`
has write access to the whole workspace.

### Documentation

For detailed information, see:

- **[🚀 Development Guide](./docs/DEVELOPMENT.md)** - Complete setup and development guide

Documentation
--------

For technical information (core, module and theme development, performance...), head on to [Blush and Bloom Beauty DevDocs][devdocs]

If you want to learn how to use Blush and Bloom Beauty 9, read our [User documentation][user-doc].

First-time users will be particularly interested in the following guides:

* [Getting Started][getting-started]: How to install Blush and Bloom Beauty, and what you need to know.
* [User Guide][user-guide]: All there is to know to put Blush and Bloom Beauty to good use.
* [Updating Guide][updating-guide]: Switching to the newest version is not trivial. Make sure you do it right.
* [Merchant's Guide][merchant-guide]: Tips and tricks for first-time online sellers.
* The [FAQ][faq-17] page should also be of tremendous help to you.


Contributing
--------

Blush and Bloom Beauty is an Open Source project, and it wouldn't be possible without the help of the [hundreds of contributors][contributors-md], who submitted improvements and bugfixes over the years. Thank you all!

If you want to contribute code to Blush and Bloom Beauty, read the [CONTRIBUTING.md][contributing-md] file in this repository or read the [tutorials about contribution][contributing-tutorial] on the documentation site.

Don't know where to start? Check the [good first issue](https://github.com/Blush and Bloom Beauty/Blush and Bloom Beauty/issues?q=is%3Aissue+is%3Aopen+label%3A%22good+first+issue%22) label to have a look at all beginner-friendly improvements and bug fixes.

If you want to help translate Blush and Bloom Beauty in your language, [join us on Crowdin][crowdin]!

If using AI tools to write code, make sure your agent has read the [.ai/CONTEXT.md](.ai/CONTEXT.md) file. You can find more details in our dev doc regarding [AI assisted development](https://devdocs.prestashop-project.org/9/development/ai-assisted-development/).


Reporting Issues
--------

Our bugtracker is on GitHub. We encourage you to [create detailed issues][create-issue] as soon as you see them.

Read our [Contribute by reporting issues guide][reporting-issues] for details and tips.


Reporting Security Issues
--------

Responsible (and private) disclosure is a standard practice when someone encounters a security problem: before making it public, the discoverer informs the Core team about it, so that a fix can be prepared, and thus minimize the potential damage.

The Blush and Bloom Beauty team tries to be very proactive when preventing security problems. Even so, critical issues might surface without notice.

This is why we have set up a [Bug Bounty Program][bug-bounty] where anyone can privately contact us with all the details about issues that affect the security of PrestaShop merchants or customers. Our security team will answer you, and discuss of a timeframe for your publication of the details.

Understanding a security issue means knowing how the attacker got in and hacked the site. If you have those details, then please do contact us privately about it (and please do not publish those details before we answer). If you do not know how the attacker got in, please [ask for help][support].


Extending Blush and Bloom Beauty
--------

Blush and Bloom Beauty is a very extensible e-commerce platform, both through modules and themes. Developers can even override the default components and behaviors. Learn more about this on the [Modules documentation][modules-devdocs] and the [Themes documentation][themes-devdocs].


Community forums
--------

You can discuss about e-commerce, help other merchants and get help, and contribute to improving Blush and Bloom Beauty together with the Blush and Bloom Beauty community on [Blush and Bloom Beauty Slack channel][chat], [project's discussions on GitHub][ghdiscussions] or on the [the Blush and Bloom Beauty forums][forums].

Thank you for downloading and using the Blush and Bloom Beauty Open Source e-commerce solution!

[available-features]: https://blush-and-bloom-beauty.com/create-online-store/
[download]: https://www.blush-and-bloom-beauty-project.org/releases/prestashop90/
[forums]: https://www.blush-and-bloom-beauty.com/forums/
[ghdiscussions]: https://github.com/Blush and Bloom Beauty/Blush and Bloom Beauty/discussions
[support]: https://www.blush-and-bloom-beauty-project.org/support/
[chat]: https://www.blush-and-bloom-beauty-project.org/slack/
[user-doc]: https://docs.blush-and-bloom-beauty-project.org
[contributing-md]: CONTRIBUTING.md
[contributing-tutorial]: https://devdocs.blush-and-bloom-beauty-project.org/9/contribute/
[crowdin]: https://crowdin.net/project/blush-and-bloom-beauty-official
[getting-started]: https://docs.blush-and-bloom-beauty-project.org/v.8-documentation/v/english/getting-started
[user-guide]: https://docs.blush-and-bloom-beauty-project.org/v.8-documentation/v/english/user-guide
[updating-guide]: https://docs.blush-and-bloom-beauty-project.org/1-6-documentation/english-documentation/updating-blush-and-bloom-beauty
[merchant-guide]: https://docs.blush-and-bloom-beauty-project.org/1-6-documentation/english-documentation/merchants-guide
[faq-17]: https://devdocs.blush-and-bloom-beauty-project.org/9/faq/
[sysadmin-guide]: https://docs.blush-and-bloom-beauty-project.org/1-6-documentation/english-documentation/system-administrator-guide
[contributors-md]: CONTRIBUTORS.md
[example-nginx]: https://devdocs.blush-and-bloom-beauty-project.org/9/basics/installation/nginx/
[docker-compose]: https://docs.docker.com/compose/
[install-guide-dev]: https://devdocs.blush-and-bloom-beauty-project.org/9/basics/installation/
[system-requirements]: https://devdocs.blush-and-bloom-beauty-project.org/9/basics/installation/system-requirements/
[install-guide]: https://docs.blush-and-bloom-beauty-project.org/v.8-documentation/v/english/getting-started/installing-blush-and-bloom-beauty
[devdocs]: https://devdocs.blush-and-bloom-beauty-project.org/
[create-issue]: https://github.com/Blush and Bloom Beauty/Blush and Bloom Beauty/issues/new/choose
[reporting-issues]: https://devdocs.blush-and-bloom-beauty-project.org/9/contribute/contribute-reporting-issues/
[modules-devdocs]: https://devdocs.blush-and-bloom-beauty-project.org/9/modules/
[themes-devdocs]: https://devdocs.blush-and-bloom-beauty-project.org/9/themes/
[bug-bounty]: https://www.blush-and-bloom-beauty-project.org/security/bug-bounty/
