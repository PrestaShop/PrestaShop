<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Sell\Product\Pricing;

use Configuration;
use Db;
use PrestaShopBundle\Form\Admin\Sell\Product\Pricing\SpecificPriceType;
use Tests\Integration\PrestaShopBundle\Form\AbstractFormTester;

class SpecificPriceTypeCombinationHelpTest extends AbstractFormTester
{
    private const SETTING = 'PS_QTY_DISCOUNT_ON_COMBINATION';

    /** @var mixed */
    private $initialSetting;

    protected function setUp(): void
    {
        parent::setUp();
        $this->initialSetting = Configuration::get(self::SETTING);
    }

    protected function tearDown(): void
    {
        Configuration::updateValue(self::SETTING, $this->initialSetting);
        Configuration::loadConfiguration();
        parent::tearDown();
    }

    public function testItWarnsWhenQuantityDiscountsAreBasedOnProducts(): void
    {
        $help = $this->combinationHelpWithSetting(0);

        $this->assertNotNull($help, 'the combination field must carry the warning');
        $this->assertStringContainsString('it will apply to all of its combinations', $help);
        // The message is only useful if it points at the setting that causes it.
        $this->assertStringContainsString('<a href="', $help);
        $this->assertStringContainsString('#configuration_fieldset_products', $help);
    }

    public function testItStaysSilentWhenQuantityDiscountsAreBasedOnCombinations(): void
    {
        $this->assertNull(
            $this->combinationHelpWithSetting(1),
            'nothing to warn about once discounts are already computed per combination'
        );
    }

    /**
     * @param int $settingValue 0 = Products, 1 = Combinations
     */
    private function combinationHelpWithSetting(int $settingValue): ?string
    {
        Configuration::updateValue(self::SETTING, $settingValue);
        Configuration::loadConfiguration();

        // The builder is enough and is the narrower subject: buildForm() runs here, while
        // getForm() would additionally resolve the impact sub-form, which needs data this
        // test has no reason to supply.
        $builder = $this->createFormBuilder(SpecificPriceType::class, [], ['product_id' => $this->combinationProductId()]);

        // The field only exists for a product that has combinations, which is the whole point
        // of the message - fail loudly rather than silently skipping the assertion.
        $this->assertTrue($builder->has('combination_id'), 'fixture product must be a combinations product');

        return $builder->get('combination_id')->getOption('help');
    }

    private function combinationProductId(): int
    {
        $id = (int) Db::getInstance()->getValue(
            'SELECT p.id_product FROM ' . _DB_PREFIX_ . 'product p
             WHERE p.product_type = "combinations"
               AND EXISTS (SELECT 1 FROM ' . _DB_PREFIX_ . 'product_attribute pa WHERE pa.id_product = p.id_product)'
        );

        if (!$id) {
            $this->markTestSkipped('no product with combinations in the fixtures');
        }

        return $id;
    }
}
