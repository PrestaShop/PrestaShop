# Testing PrestaShop

## Installing Composer & test dependencies

To easily run unit tests with [PHPUnit](https://github.com/sebastianbergmann/phpunit/), 
go into the `tests` directory, download [Composer](http://getcomposer.org/doc/00-intro.md), 
and install the test dependencies. 

<pre>
$ cd tests
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install
</pre>

## Running the tests

To run the whole test suite, use the local PHPUnit executable 
using the phpunit.xml configuration file.  

<pre>
$ vendor/bin/phpunit
</pre>