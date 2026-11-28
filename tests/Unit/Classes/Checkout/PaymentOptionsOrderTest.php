<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Classes\Checkout;

use PaymentOptionsFinder;
use PHPUnit\Framework\TestCase;

/**
 * Payment options are collected from three hooks and merged in a fixed order, with the deprecated
 * ones first. A module's position only orders it against the other modules of its own hook, so the
 * merchant order configured on paymentOptions never applied to a module answering on a legacy hook.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/42097
 */
class PaymentOptionsOrderTest extends TestCase
{
    /**
     * @var PaymentOptionsOrderTestFinder
     */
    private $finder;

    protected function setUp(): void
    {
        $this->finder = new PaymentOptionsOrderTestFinder();
    }

    public function testConfiguredModulesKeepTheirOrderAndComeFirst(): void
    {
        // What the merge produces today: the legacy module leads, the configured ones follow.
        $merged = [
            'stripe_official' => ['legacy option'],
            'paybox' => ['option'],
            'ps_wirepayment' => ['option'],
            'ps_checkpayment' => ['option'],
        ];

        // The order the merchant set on the paymentOptions hook.
        $configured = ['paybox', 'ps_wirepayment', 'ps_checkpayment'];

        $this->assertSame(
            ['paybox', 'ps_wirepayment', 'ps_checkpayment', 'stripe_official'],
            array_keys($this->finder->order($merged, $configured))
        );
    }

    public function testLegacyOnlyModulesKeepTheirRelativeOrder(): void
    {
        $merged = [
            'legacy_a' => ['option'],
            'legacy_b' => ['option'],
            'ps_wirepayment' => ['option'],
        ];

        $this->assertSame(
            ['ps_wirepayment', 'legacy_a', 'legacy_b'],
            array_keys($this->finder->order($merged, ['ps_wirepayment']))
        );
    }

    public function testNothingMovesWhenEveryModuleIsConfigured(): void
    {
        $merged = [
            'ps_wirepayment' => ['option'],
            'ps_checkpayment' => ['option'],
        ];
        $configured = ['ps_wirepayment', 'ps_checkpayment'];

        $this->assertSame($merged, $this->finder->order($merged, $configured));
    }

    public function testAConfiguredModuleThatReturnedNothingIsNotInvented(): void
    {
        $merged = ['ps_wirepayment' => ['option']];

        // ps_checkpayment is registered on the hook but produced no option for this cart.
        $ordered = $this->finder->order($merged, ['ps_wirepayment', 'ps_checkpayment']);

        $this->assertSame(['ps_wirepayment'], array_keys($ordered));
    }
}

/**
 * Exposes the protected ordering step; the surrounding find() needs a booted shop context.
 */
class PaymentOptionsOrderTestFinder extends PaymentOptionsFinder
{
    public function __construct()
    {
        // The ordering step does not touch any collaborator.
    }

    public function order(array $paymentOptions, array $configurableModules): array
    {
        return $this->putConfigurableOptionsFirst($paymentOptions, $configurableModules);
    }
}
