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

namespace Tests\Integration\PrestaShopBundle\Translation\Loader;

use PrestaShopBundle\Translation\Loader\LegacyFileLoader;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @doc ./vendor/bin/phpunit -c tests/Integration/phpunit.xml --filter="LegacyFileLoaderTest"
 */
class LegacyFileLoaderTest extends KernelTestCase
{
    public function testExtract()
    {
        self::bootKernel();
        $localeConverter = self::$kernel->getContainer()->get('prestashop.core.translation.locale.converter');
        $extractor = new LegacyFileLoader($localeConverter);
        $catalogue = $extractor->load($this->getTranslationsFolder(), 'fr-FR');

        $this->assertInstanceOf(MessageCatalogueInterface::class, $catalogue);
        $this->assertCount(88, $catalogue->all('messages'));
        $someId = 'c7e728f436eee2692d6c6f756621a70e';
        $this->assertTrue($catalogue->has($someId));
        $this->assertSame(
            'Une erreur est survenue, veuillez vérifier votre fichier zip',
            $catalogue->get($someId)
        );
    }

    protected function tearDown()
    {
        self::$kernel->shutdown();
    }

    /**
     * @return string
     */
    private function getTranslationsFolder()
    {
        return __DIR__ . '/../../../../resources/some_module/translations/';
    }
}
