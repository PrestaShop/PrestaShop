<?php
/**
 * 2007-2019 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context;

use AppKernel;
use Context;

class LocaleFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * PrestaShop Symfony AppKernel
     *
     * Required to access services through the container
     *
     * @var AppKernel
     */
    protected static $kernel;

    /**
     * @BeforeSuite
     */
    public static function prepare($scope)
    {
        require_once __DIR__ . '/../../bootstrap.php';
        self::$kernel = new AppKernel('test', true);
        self::$kernel->boot();
    }

    /**
     * @Given I set the translation locale to :locale
     */
    public function iSetTheTranslationLocaleTo($locale)
    {
        Context::getContext()->getTranslator()->setLocale($locale);
        $this::getContainer()->get('translator')->setLocale($locale);
    }

    /**
     * Return PrestaShop Symfony services container
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public static function getContainer()
    {
        return static::$kernel->getContainer();
    }
}
