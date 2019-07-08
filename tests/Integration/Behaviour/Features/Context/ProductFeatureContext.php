<?php

namespace Tests\Integration\Behaviour\Features\Context;

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
     * @Then /^product grid in BO should not contain column "([^"]*)"$/
     */
    public function productGridInBOShouldNotContainColumn($columnId)
    {
        $gridDefinitionId = 'prestashop.core.grid.definition.product';
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
