About PrestaShop
--------

[![Build Status](https://travis-ci.org/PrestaShop/PrestaShop.svg?branch=develop)](https://travis-ci.org/PrestaShop/PrestaShop)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/a798dc20a1254776aa7a8a0d8bd8d331)](https://www.codacy.com/app/PrestaShop/PrestaShop?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=PrestaShop/PrestaShop&amp;utm_campaign=Badge_Grade)
[![Gitter chat](https://badges.gitter.im/PrestaShop/PrestaShop.png)](https://gitter.im/PrestaShop/General)


PrestaShop is an Open Source e-commerce web application, committed to providing the best shopping cart experience for both merchants and customers. It is written in PHP, is highly customizable, supports all the major payment services, is translated in many languages and localized for many countries, has a fully responsive design (both front and back office), etc. [See all the available features][available-features].

<p align="center">
  <img src="https://www.prestashop.com/1.7/assets/img/product.png" alt="PrestaShop 1.7 back office"/>
</p>

This repository contains the source code of PrestaShop, which is intended for development and preview only. To download the latest stable public version of PrestaShop (currently, version 1.7), please go to [the download page][2] on the official PrestaShop site.


About the 'develop' branch
--------

The 'develop' branch of this repository contains the work in progress source code for the next version of PrestaShop 1.7.
 
For more information on our branch system, read our guide on [installing PrestaShop for development][install-guide-dev].

PRESTASHOP 1.7 IS NOW PRODUCTION-READY! Its first stable version, 1.7.0.0, was released on November 7th, 2016. Further updates have been released since then. Learn more about it on [the Build devblog](http://build.prestashop.com/tag/1.7/).

Server configuration
--------

To install PrestaShop 1.7, you need a web server running PHP 5.6+ and any flavor of MySQL 5.0+ (MySQL, MariaDB, Percona Server, etc.).

You will also need a database administration tool, such as phpMyAdmin, in order to create a database for PrestaShop.
We recommend the Apache or Nginx web servers (check out our [example Nginx configuration file][example-nginx]).

You can find more information on our [System requirements][system-requirements] page and on the [System Administrator Guide][sysadmin-guide].

Installation
--------

If you downloaded the source code from GitHub, read our guide on [installing PrestaShop for development][install-guide-dev]. If you intend to install a production shop, make sure to download the latest version from [our download page][download], then read the [install guide for users][install-guide].

Docker compose
--------

PrestaShop can also be deployed with Docker and its tool [Docker compose][docker-compose].

To run the software, use:

```
docker-compose up
```

Then reach your shop on this URL: http://localhost:8001

Docker will bind your port 8001 to the web server. If you want to use other port, open and modify the file `docker-compose.yml`.
MySQL credentials can also be found and modified in this file if needed.

**Note:**  Before auto-installing PrestaShop, this container checks the file *config/settings.inc.php* does not exist on startup.
If you expect the container to (re)install your shop, remove this file if it exists.

Documentation
--------

For technical information (core, module and theme development, performance...), head on to [PrestaShop DevDocs][devdocs]

If you want to learn how to use PrestaShop 1.7, read our [User documentation][user-doc].

First-time users will be particularly interested in the following guides:

* [Getting Started][getting-started]: How to install PrestaShop, and what you need to know.
* [User Guide][user-guide]: All there is to know to put PrestaShop to good use.
* [Updating Guide][updating-guide]: Switching to the newest version is not trivial. Make sure you do it right.
* [Merchant's Guide][merchant-guide]: Tips and tricks for first-time online sellers.
* The [FAQ][faq-17] and the [Troubleshooting][troubleshooting] pages should also be of tremendous help to you.


Contributing
--------

PrestaShop is an Open Source project, and it wouldn't be possible without the help of the [hundreds of contributors][contributors-md], who submitted improvements and bugfixes over the years. Thank you all!

If you want to contribute code to PrestaShop, read the [CONTRIBUTING.md][contributing-md] file in this repository or read the [tutorials about contribution][contributing-tutorial] on the documentation site.

If you want to help translate PrestaShop in your language, [join us on Crowdin][crowdin]!

Current Crowdin status (for 69 registered languages): [![Crowdin](https://crowdin.net/badges/prestashop-official/localized.png)](https://crowdin.net/project/prestashop-official)

Reporting Issues
--------

Our bugtracker is called the Forge. We encourage you to [create detailed issues][create-issue] as soon as you see them.

Read our [Contribute by reporting issues guide][reporting-issues] for details and tips.


Reporting Security Issues
--------

Responsible (and private) disclosure is a standard practice when someone encounters a security problem: before making it public, the discoverer informs the Core team about it, so that a fix can be prepared, and thus minimize the potential damage.

The PrestaShop team tries to be very proactive when preventing security problems. Even so, critical issues might surface without notice.

This is why we have set up the [security@prestashop.com](mailto:security@prestashop.com) email address: anyone can privately contact us with all the details about issues that affect the security of PrestaShop merchants or customers. Our security team will answer you, and discuss of a timeframe for your publication of the details.

Understanding a security issue means knowing how the attacker got in and hacked the site. If you have those details, then please do contact us privately about it (and please do not publish those details before we answer). If you do not know how the attacker got in, please ask for help on the support forums.


Extending PrestaShop
--------

PrestaShop is a very extensible e-commerce platform, both through modules and themes. Developers can even override the default components and behaviors. Learn more about this on the [Modules documentation][modules-devdocs] and the [Themes documentation][themes-devdocs].

Themes and modules can be obtained (and sold!) on [PrestaShop Addons][addons], the official marketplace for PrestaShop.


Community forums
--------

You can discuss about e-commerce, help other merchants and get help, and contribute to improving PrestaShop together with the PrestaShop community on [the PrestaShop forums][forums].

Thank you for downloading and using the PrestaShop Open Source e-commerce solution!

[available-features]: https://www.prestashop.com/en/online-store-builder
[download]: https://www.prestashop.com/en/download
[forums]: https://www.prestashop.com/forums/
[user-doc]: http://doc.prestashop.com
[contributing-md]: CONTRIBUTING.md
[contributing-tutorial]: http://doc.prestashop.com/display/PS16/Contributing+to+PrestaShop
[crowdin]: https://crowdin.net/project/prestashop-official
[getting-started]: http://doc.prestashop.com/display/PS17/Getting+Started
[user-guide]: http://doc.prestashop.com/display/PS17/User+Guide
[updating-guide]: http://doc.prestashop.com/display/PS16/Updating+PrestaShop
[merchant-guide]: http://doc.prestashop.com/display/PS16/Merchant%27s+Guide
[faq-17]: http://build.prestashop.com/news/prestashop-1-7-faq/
[troubleshooting]: http://doc.prestashop.com/display/PS16/Troubleshooting
[sysadmin-guide]: http://doc.prestashop.com/display/PS16/System+Administrator+Guide
[addons]: https://addons.prestashop.com/
[contributors-md]: CONTRIBUTORS.md
[example-nginx]: docs/server_config/nginx.conf.dist
[docker-compose]: https://docs.docker.com/compose/
[install-guide-dev]: https://devdocs.prestashop.com/1.7/basics/installation/
[system-requirements]: https://devdocs.prestashop.com/1.7/basics/installation/system-requirements/
[install-guide]: http://doc.prestashop.com/display/PS17/Installing+PrestaShop
[devdocs]: https://devdocs.prestashop.com/
[create-issue]: http://forge.prestashop.com/secure/CreateIssue%21default.jspa?selectedProjectId=11322&issuetype=1
[reporting-issues]: https://devdocs.prestashop.com/1.7/contribute/contribute-reporting-issues/
[modules-devdocs]: https://devdocs.prestashop.com/1.7/modules/
[themes-devdocs]: https://devdocs.prestashop.com/1.7/themes/
