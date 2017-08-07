About PrestaShop
--------

[![Build Status](https://travis-ci.org/PrestaShop/PrestaShop.svg?branch=develop)](https://travis-ci.org/PrestaShop/PrestaShop)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/a798dc20a1254776aa7a8a0d8bd8d331)](https://www.codacy.com/app/PrestaShop/PrestaShop?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=PrestaShop/PrestaShop&amp;utm_campaign=Badge_Grade)

PrestaShop is a free and Open Source e-commerce web application, committed to providing the best shopping cart experience for both merchants and customers. It is written in PHP, is highly customizable, supports all the major payment services, is translated in many languages and localized for many countries, has a fully responsive design (both front and back office), etc. [See all the available features][1].

<p align="center">
  <img src="https://www.prestashop.com/1.7/assets/img/product.png" alt="PrestaShop 1.7 back office"/>
</p>

To download the latest stable public version of PrestaShop (currently, version 1.7), please go to [the download page][2] on the official PrestaShop site.


About the 'develop' branch
--------

The 'develop' branch of this repository contains the source code for the latest version of PrestaShop 1.7.

PRESTASHOP 1.7 IS NOW PRODUCTION-READY! Its first stable version, 1.7.0.0, was released on November 7th, 2016. Further updates have been released since then. Learn more about it on [the Build devblog](http://build.prestashop.com/tag/1.7/).

You can click the "Download ZIP" button from the root of this repository to download the current state of PrestaShop 1.7.  
If you prefer to download the regular 1.7 package, you can find the latest version on the [the download page][2].

Also, the ZIP file does not contain the default modules. Since the 1.6 theme needs these module, the store will not display much as-is, even if you install the Starter Theme. We therefore advise you to focus your tests on the back office for the time being -- unless you are helping the team improve the Starter Theme.

Finally, the ZIP file contains resources for developers and designers that are not in the public archive, such as the unit testing files (in the /tests folder).


Server configuration
--------

To install PrestaShop 1.7, you need a web server running PHP 5.4+ and any flavor of MySQL 5.0+ (MySQL, MariaDB, Percona Server, etc.).

You will also need a database administration tool, such as phpMyAdmin, in order to create a database for PrestaShop.
We recommend the Apache or Nginx web servers (check out our [example Nginx configuration file][23]).

You can find more information on the [System Administrator Guide][19].

If your host does not offer PHP 5 by default, you will find a few explanations about PHP 5 or the `.htaccess` file in [our documentation][3], with details for certain hosting services.

If you want your own store with nothing to download and install, you should use [PrestaShop Cloud][4], our 100% free and fully-hosted PrestaShop service: it lets you create your online store in less than 10 minutes without any technical knowledge. Learn more about the [difference between PrestaShop Cloud and PrestaShop Download][10].


Installation
--------

Once the files in the PrestaShop archive have been decompressed and uploaded on your hosting space, go to the root of your PrestaShop directory with your web browser, and the PrestaShop unzipper/installer will start automatically. Follow the instructions until PrestaShop is installed.

If you get any PHP error, it might be that you do not have PHP 5 on your web server, or that you need to activate it. See [this page for explanations about PHP 5][3], or contact your web host directly.  
If you do not find any solution to start the installer, please post about your issue on [the PrestaShop forums][5].

If you installed PrestaShop from GitHub:

* Install Composer ([https://getcomposer.org][22])
* Then run:

>
    composer install

User documentation
--------

The official PrestaShop 1.7 documentation is available online [on its own website][6]

First-time users will be particularly interested in the following guides:
* [Getting Started][13]: How to install PrestaShop, and what you need to know.
* [User Guide][14]: All there is to know to put PrestaShop to good use.
* [Updating Guide][15]: Switching to the newest version is not trivial. Make sure you do it right.
* [Merchant's Guide][16]: Tips and tricks for first-time online sellers.
* The [FAQ][17] and the [Troubleshooting][18] pages should also be of tremendous help to you.


Contributing
--------

PrestaShop is an Open Source project, and it wouldn't be possible without the help of the [hundreds of contributors][21], who submitted improvements and bugfixes over the years. Thank you all!

If you want to contribute code to PrestaShop, read the [CONTRIBUTING.md][7] file in this repository or read the [tutorials about contribution][8] on the documentation site.

Current [Travis](https://travis-ci.org/) status: [![Travis](https://travis-ci.org/PrestaShop/PrestaShop.svg?branch=develop)](https://travis-ci.org/PrestaShop/PrestaShop) (The Unit Tests are being implemented, so the status might be broken).

If you want to help translate PrestaShop in your language, [join us on Crowdin][9]!

Current Crowdin status (for 69 registered languages): [![Crowdin](https://crowdin.net/badges/prestashop-official/localized.png)](https://crowdin.net/project/prestashop-official)

Reporting Issues
--------

Our bugtracker is called the Forge. We encourage you to [create detailed issues](http://forge.prestashop.com/secure/CreateIssue%21default.jspa?selectedProjectId=11322&issuetype=1) as soon as you see them.

See our [Forge Guide](http://doc.prestashop.com/display/PS16/How+to+use+the+Forge+to+contribute+to+PrestaShop) with details and tips.


Reporting Security Issues
--------

Responsible (and private) disclosure is a standard practice when someone encounters a security problem: before making it public, the discoverer informs the Core team about it, so that a fix can be prepared, and thus minimize the potential damage.

The PrestaShop team tries to be very proactive when preventing security problems. Even so, critical issues might surface without notice.

This is why we have set up the [security@prestashop.com](mailto:security@prestashop.com) email address: anyone can privately contact us with all the details about issues that affect the security of PrestaShop merchants or customers. Our security team will answer you, and discuss of a timeframe for your publication of the details.

Understanding a security issue means knowing how the attacker got in and hacked the site. If you have those details, then please do contact us privately about it (and please do not publish those details before we answered). If you do not know how the attacker got in, please ask for help on the support forums.


Extending PrestaShop
--------

PrestaShop is a very extensible e-commerce platform, both through modules and themes. Developers can even override the default components and behaviors. Learn more about this using the [Developer Guide][11] and the [Designer Guide][12].

Themes and modules can be obtained (and sold!) from [PrestaShop Addons][20], the official marketplace for PrestaShop.


Community forums
--------

You can discuss about e-commerce, help other merchants and get help, and contribute to improving PrestaShop together with the PrestaShop community on [the PrestaShop forums][5].


Getting support
--------

If you need help using PrestaShop 1.7, ask on the forums: https://www.prestashop.com/forums/forum/273-170x-in-development/


Thank you for downloading and using the PrestaShop Open Source e-commerce solution!

[1]: https://www.prestashop.com/en/online-store-builder
[2]: https://www.prestashop.com/en/download
[3]: http://doc.prestashop.com/display/PS16/Misc.+information#Misc.information-ActivatingPHP5
[4]: https://www.prestashop.com
[5]: https://www.prestashop.com/forums/
[6]: http://doc.prestashop.com
[7]: CONTRIBUTING.md
[8]: http://doc.prestashop.com/display/PS16/Contributing+to+PrestaShop
[9]: https://crowdin.net/project/prestashop-official
[10]: https://www.prestashop.com/en/ecommerce-software
[11]: http://developers.prestashop.com/
[12]: http://developers.prestashop.com/
[13]: http://doc.prestashop.com/display/PS17/Getting+Started
[14]: http://doc.prestashop.com/display/PS17/User+Guide
[15]: http://doc.prestashop.com/display/PS17/Updating+PrestaShop
[16]: http://doc.prestashop.com/display/PS16/Merchant%27s+Guide
[17]: http://build.prestashop.com/news/prestashop-1-7-faq/
[18]: http://doc.prestashop.com/display/PS16/Troubleshooting
[19]: http://doc.prestashop.com/display/PS16/System+Administrator+Guide
[20]: https://addons.prestashop.com/
[21]: CONTRIBUTORS.md
[22]: https://getcomposer.org
[23]: docs/server_config/nginx.conf.dist
