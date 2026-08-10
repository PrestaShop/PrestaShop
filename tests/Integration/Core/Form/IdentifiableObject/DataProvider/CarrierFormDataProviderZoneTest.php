<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Form\IdentifiableObject\DataProvider;

use Cache;
use Db;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\CarrierFormDataProvider;
use Tests\Integration\PrestaShopBundle\Form\FormListenerTestCase;

/**
 * A zone keeps the ranges of a carrier once it is disabled, so it stays listed on the carrier page.
 * Nothing on the row said so, which left no way to tell why the carrier is not offered there.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/37171
 */
class CarrierFormDataProviderZoneTest extends FormListenerTestCase
{
    private const CARRIER_ID = 2;

    private int $zoneId;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ShopContextBuilder $shopContextBuilder */
        $shopContextBuilder = self::getContainer()->get('test_shop_context_builder');
        $shopContextBuilder->setShopId(1);
        $shopContextBuilder->setShopConstraint(ShopConstraint::shop(1));

        /** @var LanguageContextBuilder $languageContextBuilder */
        $languageContextBuilder = self::getContainer()->get('test_language_context_builder');
        $languageContextBuilder->setLanguageId(1);

        $this->zoneId = (int) Db::getInstance()->getValue(
            'SELECT id_zone FROM ' . _DB_PREFIX_ . 'delivery WHERE id_carrier = ' . self::CARRIER_ID . ' AND id_zone > 0'
        );
    }

    protected function tearDown(): void
    {
        $this->setZoneActive(true);

        parent::tearDown();
    }

    public function testAnActiveZoneIsNamedAsItIs(): void
    {
        $this->setZoneActive(true);

        $this->assertStringNotContainsString('(', $this->zoneLabel());
    }

    public function testADisabledZoneSaysSoOnItsRow(): void
    {
        $activeLabel = $this->zoneLabel();

        $this->setZoneActive(false);

        $this->assertSame($activeLabel . ' (Inactive)', $this->zoneLabel());
    }

    private function zoneLabel(): string
    {
        /** @var CarrierFormDataProvider $dataProvider */
        $dataProvider = self::getContainer()->get(CarrierFormDataProvider::class);
        $data = $dataProvider->getData(self::CARRIER_ID);

        foreach ($data['shipping_settings']['ranges_costs'] as $zone) {
            if ((int) $zone['zoneId'] === $this->zoneId) {
                return $zone['zoneName'];
            }
        }

        $this->fail(sprintf('Zone %d is not listed for carrier %d.', $this->zoneId, self::CARRIER_ID));
    }

    private function setZoneActive(bool $active): void
    {
        Db::getInstance()->update('zone', ['active' => (int) $active], 'id_zone = ' . $this->zoneId);
        Cache::clean('*');
    }
}
