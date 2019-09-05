<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\PrestaShopBundle\Translation\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Provider\ExternalModuleLegacySystemProvider;
use Symfony\Component\Translation\Loader\LoaderInterface;
use PrestaShopBundle\Translation\Provider\SearchProvider;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Tests the search translations provider
 */
class SearchProviderTest extends TestCase
{
    /**
     * @var SearchProvider
     */
    private $provider;

    protected function setUp()
    {
        $loader = $this->getMockBuilder(LoaderInterface::class)
            ->getMock()
        ;
        $externalSystemProvider = $this->getMockBuilder(ExternalModuleLegacySystemProvider::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $resourcesDir = __DIR__ . '/../../../../Resources/translations';
        $this->provider = new SearchProvider(
            $loader,
            $externalSystemProvider,
            $resourcesDir,
            ''
        );

        $this->provider->setDomain('AdminActions');
        $this->provider->setLocale('fr-FR');
    }

    public function testItExtractsOnlyTheSelectedCataloguesFromXliffFiles()
    {
        $catalogue = $this->provider->getMessageCatalogue();
        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check that only the selected domain is in the catalogue
        $this->assertSame(['AdminActions'], $catalogue->getDomains());

        // Check integrity of translations
        $this->assertArrayHasKey('AdminActions', $catalogue->all());

        $adminTranslations = $catalogue->all('AdminActions');
        $this->assertCount(91, $adminTranslations);
        $this->assertSame('Télécharger le fichier', $catalogue->get('Download file', 'AdminActions'));
    }
}
