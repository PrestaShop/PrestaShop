Integration tests
=================

## Run

Run tests using phpunit binary using the phpunit.xml configuration file

```
# from the PrestaShop root folder
php -d date.timezone=UTC ./vendor/bin/phpunit -c tests/Integration/phpunit.xml
```

## Run behat

Run tests using behat binary using the right behat.yml configuration file

```
# from the PrestaShop root folder
php -d date.timezone=UTC ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml
```

## Add new behat tests

You can add a new test in one of the feature files or create a new feature file. It must use the
[gherkin][1] syntax.

If you create new steps, you can add them to one of the available FeatureContext if the step belongs
to it or create a new FeatureContext if you think it should be a dedicated file. In this case, update the
`behat.yml` file to include your new Context.

FeatureContexts are split by features: cart steps should go into CartFeatureContext.

## Insights

This tests are powered by [behat][2].

To sum it up:
- features file are being parsed by behat following gherkin syntax
- behat matches each scenario step with a regular expression that must be provided in one the available feature contexts
- the regexp indicates a php function to be run by behat
- behat provides hooking capabilities to handle the test lifecycle (application boot, database reset, cache clear...)

### PrestaShop behat tests

Feature files are stored in `Features` folder.

FeatureContext files are stored in `Features/Context` folder. They must extend
the `AbstractPrestaShopFeatureContext` that provide the setup necessary to perform
tests on PrestaShop.

[1]: http://docs.behat.org/en/v2.5/guides/1.gherkin.html
[2]: http://behat.org/en/latest/
