<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Controller\Front;

use Configuration;
use ProductController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Google reports a product as missing hasMerchantReturnPolicy when the shop publishes no return policy
 * in its structured data. The shop already records whether it accepts returns and for how long, so the
 * policy is built from that rather than from anything the merchant has to restate.
 */
class ProductMerchantReturnPolicyTest extends KernelTestCase
{
    private string $originalOrderReturn;
    private string $originalReturnDays;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->originalOrderReturn = (string) Configuration::get('PS_ORDER_RETURN');
        $this->originalReturnDays = (string) Configuration::get('PS_ORDER_RETURN_NB_DAYS');
    }

    protected function tearDown(): void
    {
        Configuration::updateValue('PS_ORDER_RETURN', $this->originalOrderReturn);
        Configuration::updateValue('PS_ORDER_RETURN_NB_DAYS', $this->originalReturnDays);

        parent::tearDown();
    }

    public function testNoPolicyIsPublishedWhenReturnsAreDisabled(): void
    {
        Configuration::updateValue('PS_ORDER_RETURN', 0);

        $this->assertNull($this->buildPolicy());
    }

    public function testAReturnWindowIsPublishedAsAFiniteWindow(): void
    {
        Configuration::updateValue('PS_ORDER_RETURN', 1);
        Configuration::updateValue('PS_ORDER_RETURN_NB_DAYS', 14);

        $policy = $this->buildPolicy();

        $this->assertNotNull($policy);
        $this->assertSame('MerchantReturnPolicy', $policy['@type']);
        $this->assertSame('https://schema.org/MerchantReturnFiniteReturnWindow', $policy['returnPolicyCategory']);
        $this->assertSame(14, $policy['merchantReturnDays']);
        $this->assertNotEmpty($policy['applicableCountry']);
    }

    /**
     * Order::getNumberOfDays() accepts any return once PS_ORDER_RETURN_NB_DAYS is 0, so zero is an
     * unlimited window. Published verbatim as merchantReturnDays it would say the opposite.
     */
    public function testAZeroReturnWindowIsPublishedAsUnlimitedRatherThanZeroDays(): void
    {
        Configuration::updateValue('PS_ORDER_RETURN', 1);
        Configuration::updateValue('PS_ORDER_RETURN_NB_DAYS', 0);

        $policy = $this->buildPolicy();

        $this->assertNotNull($policy);
        $this->assertSame('https://schema.org/MerchantReturnUnlimitedWindow', $policy['returnPolicyCategory']);
        $this->assertArrayNotHasKey('merchantReturnDays', $policy);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildPolicy(): ?array
    {
        return (new class() extends ProductController {
            public function buildMerchantReturnPolicy(): ?array
            {
                return $this->getMerchantReturnPolicyStructuredData();
            }
        })->buildMerchantReturnPolicy();
    }
}
