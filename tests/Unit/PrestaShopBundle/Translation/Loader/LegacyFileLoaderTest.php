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

namespace Tests\Unit\PrestaShopBundle\Translation\Loader;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Loader\LegacyFileLoader;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * @doc ./vendor/bin/phpunit -c tests/Unit/phpunit.xml --filter="LegacyFileLoaderTest"
 */
class LegacyFileExtractorTest extends TestCase
{
    public function testExtract()
    {
        $extractor = new LegacyFileLoader();
        $catalogue = $extractor->load($this->getTranslationsFolder(), 'fr-FR');

        $this->assertInstanceOf(MessageCatalogueInterface::class, $catalogue);
        $this->assertCount(5, $catalogue->all('ModulesSomeModule'));
    }

    /**
     * @return string
     */
    private function getTranslationsFolder()
    {
        return __DIR__ . '/../../../../resources/some-module/translations/';
    }
}
