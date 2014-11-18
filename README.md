About PrestaShop
--------

PrestaShop is a free and open-source e-commerce web application, committed to providing the best shopping cart experience for both merchants and customers. It is written in PHP, is highly customizable, supports all the major payment services, is translated in many languages and localized for many countries, is fully responsive (both front- and back-office), etc. [See all the available features][1].

<p align="center">
  <img src="http://www.prestashop.com/images/banners/general/ps16-screenshot-github.png" alt="PrestaShop's back-office dashboard"/>
</p>

To download the latest stable version of PrestaShop, go to [the download page][2] on the official PrestaShop site.


About this repository
--------

This repository contains the latest version of PrestaShop: version 1.6. You can learn more about the new features of this version on [the progress page][3].

Clicking the "Download ZIP" button from the root of this repository will download the current state of PrestaShop 1.6 -- a branch that is in active development, and ready for production use. Note that the ZIP file will not contain the default modules: you need to make a recursive clone using Git in order to download these files too. See [CONTRIBUTING.md][8] for more information about using Git.


Server configuration
--------

To install PrestaShop, you need a web server running PHP 5 and any flavor of MySQL 5 (MySQL, MariaDB, Percona Server, etc.).
You will also need a database administration tool, such as phpMyAdmin, in order to create a database for PrestaShop.
We recommend the Apache or Nginx web servers.

If your host does not offer PHP 5 by default, [here are a few explanations][4] about PHP 5 or the .htaccess file for certain hosting services (1&amp;1, Free.fr, OVH, Infomaniak, Amen, GoDaddy, etc.).

If you want your own store with nothing to download and install, visit [http://www.prestabox.com][5]: it lets you create your online store in less than 10 minutes without any technical knowledge.


Installation
--------

Once the files in the PrestaShop archive have been decompressed and uploaded on your hosting space, go to the root of your PrestaShop directory with your web browser, and the PrestaShop installer will start automatically. Follow the instructions until PrestaShop is installed.

If you get any PHP error, it might be that you do not have PHP 5 on your web server, or that you need to activate it. See [this page for explanations about PHP 5][4], or contact your web host directly.
If you do not find any solution to start the installer, please post about your issue on [the PrestaShop forums][6].


Documentation
--------

The official PrestaShop documentation is available online [on its own website][7].


Contributing
--------

If you want to contribute code to PrestaShop, read the [CONTRIBUTING.md][8] file in this repository or read the [tutorials about contribution][9] on the documentation site.

If you want to help translate PrestaShop in your language, [join us on Crowdin][10]!

[![Crowdin](https://crowdin.net/badges/prestashop-official/localized.png)](https://crowdin.net/project/prestashop-official)


Forums
--------

You can discuss about e-commerce, help other merchants and get help, and contribute to improving PrestaShop together with the PrestaShop community on [the PrestaShop forums][6].


Support
--------

If you need help using PrestaShop 1.6, contact the PrestaShop support team: http://support.prestashop.com/.


Thank you for downloading and using the PrestaShop e-commerce Open-source solution!

[1]: http://www.prestashop.com/en/features
[2]: http://www.prestashop.com/en/download
[3]: http://www.prestashop.com/en/progress-1-6
[4]: http://doc.prestashop.com/display/PS16/Misc.+information#Misc.information-ActivatingPHP5
[5]: http://www.prestabox.com
[6]: http://www.prestashop.com/forums/
[7]: http://doc.prestashop.com
[8]: CONTRIBUTING.md
[9]: http://doc.prestashop.com/display/PS16/Contributing+to+PrestaShop
[10]: https://crowdin.net/project/prestashop-official
