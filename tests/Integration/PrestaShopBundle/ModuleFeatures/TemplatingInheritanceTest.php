<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\PrestaShopBundle\ModuleFeatures;

use Tests\Integration\PrestaShopBundle\Test\WebTestCase;

/**
 * @group module-features
 */
class TemplatingInheritanceTest extends WebTestCase
{
    /**
     * @var string $moduleName The module name.
     */
    const MODULE_NAME = 'module_inheritance';

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        $this->installModule(self::MODULE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->uninstallModule(self::MODULE_NAME);
    }

    /**
     * @dataProvider getDataProvider
     */
    public function testTemplatesShouldHaveBeenOverridden($pageName, $pageTitle, $route)
    {
        $this->client->request('GET', $this->get('router')->generate($route));
        $response = $this->client->getResponse();

        self::assertTrue($response->isSuccessful(), "$pageName page should be available");
        self::assertContains($pageTitle, $response->getContent(), "$pageName page should contains $pageTitle");
    }

    /**
     * @return array contains data to be tested:
     * - the overridden pages
     * - with the pages content
     * - and the route name for each page.
     */
    public function getDataProvider()
    {
        return [
            'administration_page' => ['Administration', 'Administration Page', 'admin_administration'],
        ];
    }
}