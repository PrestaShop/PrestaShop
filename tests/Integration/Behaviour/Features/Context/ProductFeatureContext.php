<?php

namespace Tests\Integration\Behaviour\Features\Context;

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
}
