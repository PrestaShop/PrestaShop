<?php

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Tester\Exception\PendingException;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ProductGridDefinitionFactory;
use RuntimeException;

class ProductFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * This is used for TYPE_HTML type for object model field - in testing context this directory is not being
     * created.
     *
     * @BeforeSuite
     */
    public static function enableHtmlPurifier($event)
    {
        $container = CommonFeatureContext::getContainer();

        $purifierCacheDirectory = _PS_CACHE_DIR_ . 'purifier';
        $filesystem = $container->get('filesystem');

        if (!$filesystem->exists($purifierCacheDirectory)) {
            $filesystem->mkdir($purifierCacheDirectory);
        }
    }

    /**
     * @Then /^grid definition "([^"]*)" should contain column with id "([^"]*)"$/
     */
    public function assertGridDefinitionShouldContainColumnWithId($gridDefinitionId, $columnId)
    {
        /** @var GridDefinitionFactoryInterface $gridDefinition */
        $gridDefinition = CommonFeatureContext::getContainer()->get($gridDefinitionId);

        /** @var array $columns */
        $columns = $gridDefinition->getDefinition()->getColumns()->toArray();
        $columnIds = array_column($columns, 'id');

        if (!in_array($columnId, $columnIds, true)) {
            throw new RuntimeException(
                sprintf(
                    'For grid definition "%s" missing columnId "%s".',
                    $gridDefinitionId,
                    $columnId
                )
            );
        }
    }

    /**
     * @todo: ask why this part does not work.
     *
     * @Then /^grid definition "([^"]*)" should not contain column with id "([^"]*)"$/
     */
    public function assertGridDefinitionShouldNotContainColumnWithId($gridDefinitionId, $columnId)
    {
        /** @var ProductGridDefinitionFactory $gridDefinition */
        $gridDefinition = CommonFeatureContext::getContainer()->get($gridDefinitionId);

        /** @var array $columns */
        $columns = $gridDefinition->getDefinition()->getColumns()->toArray();
        $columnIds = array_column($columns, 'id');

        if (in_array($columnId, $columnIds, true)) {
            throw new RuntimeException(
                sprintf(
                    'For grid definition "%s"  columnId "%s" exists',
                    $gridDefinitionId,
                    $columnId
                )
            );
        }
    }
}
