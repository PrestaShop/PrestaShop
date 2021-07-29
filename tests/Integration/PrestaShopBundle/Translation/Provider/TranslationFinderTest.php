<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Translation\Provider;

use PrestaShopBundle\Translation\Provider\TranslationFinder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests the provider for backoffice translations
 */
class TranslationFinderTest extends KernelTestCase
{
    /**
     * @var TranslationFinder
     */
    private $finder;
    /**
     * @var string
     */
    private $resourcesDir;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->resourcesDir = self::$kernel->getContainer()->getParameter('test_translations_dir');

        $this->finder = new TranslationFinder();
    }

    public function testGetCatalogueFromPathsReturnsAllDomainsIfNoPatternGiven(): void
    {
        $catalogue = $this->finder->getCatalogueFromPaths([$this->resourcesDir], 'fr-FR');

        $domains = $catalogue->getDomains();
        sort($domains, SORT_STRING);

        $this->assertSame([
            'AdminActions',
            'EmailsBody',
            'EmailsSubject',
            'ModulesCheckpaymentAdmin',
            'ModulesCheckpaymentShop',
            'ModulesWirepaymentAdmin',
            'ModulesWirepaymentShop',
            'ModulesXlftranslatedmoduleAdmin',
            'ShopNotificationsWarning',
            'messages',
        ], $domains);
    }

    public function testGetCatalogueFromPathsFiltersDomainsIfPatternIsGiven(): void
    {
        $catalogue = $this->finder->getCatalogueFromPaths([$this->resourcesDir], 'fr-FR', '#^Admin[A-Z]#');

        $this->assertSame([
            'AdminActions',
        ], $catalogue->getDomains());

        $catalogue = $this->finder->getCatalogueFromPaths([$this->resourcesDir], 'fr-FR', '#^Modules[A-Z]#');

        $domains = $catalogue->getDomains();
        sort($domains, SORT_STRING);

        $this->assertSame([
            'ModulesCheckpaymentAdmin',
            'ModulesCheckpaymentShop',
            'ModulesWirepaymentAdmin',
            'ModulesWirepaymentShop',
            'ModulesXlftranslatedmoduleAdmin',
        ], $domains);
    }
}
