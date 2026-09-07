<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Checkout;

use CheckoutPaymentStep;
use CheckoutProcess;
use CheckoutSession;
use ConditionsToApproveFinder;
use Context;
use Language;
use PaymentOptionsFinder;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Every other checkout step keeps what the customer chose across requests - the addresses step through
 * getDataToPersist(), the delivery step through the cart's delivery_option column. The payment step kept
 * nothing, so without JavaScript, where the choice travels as a GET parameter, picking a payment method
 * and then reloading the checkout silently reset the selection.
 */
class CheckoutPaymentStepTest extends TestCase
{
    private CheckoutPaymentStep $step;

    protected function setUp(): void
    {
        parent::setUp();

        $context = $this->createMock(Context::class);
        $context->language = $this->createMock(Language::class);

        $this->step = new CheckoutPaymentStep(
            $context,
            $this->createMock(TranslatorInterface::class),
            $this->createMock(PaymentOptionsFinder::class),
            $this->createMock(ConditionsToApproveFinder::class)
        );
        $this->step->setCheckoutProcess(
            new CheckoutProcess($context, $this->createMock(CheckoutSession::class))
        );
    }

    public function testTheChosenPaymentOptionIsHandedToTheCheckoutSessionData(): void
    {
        $this->step->handleRequest(['select_payment_option' => 'ps_checkpayment-2']);

        $this->assertSame(
            ['selected_payment_option' => 'ps_checkpayment-2'],
            $this->step->getDataToPersist()
        );
    }

    public function testAChoiceMadeOnAnEarlierRequestSurvives(): void
    {
        $this->step->restorePersistedData(['selected_payment_option' => 'ps_wirepayment-1']);
        $this->step->handleRequest([]);

        $this->assertSame(
            ['selected_payment_option' => 'ps_wirepayment-1'],
            $this->step->getDataToPersist()
        );
    }

    public function testChangingTheOptionOverwritesWhatWasRemembered(): void
    {
        // OrderController restores first and handles the request second, so the newer choice has to win.
        $this->step->restorePersistedData(['selected_payment_option' => 'ps_wirepayment-1']);
        $this->step->handleRequest(['select_payment_option' => 'ps_checkpayment-2']);

        $this->assertSame(
            ['selected_payment_option' => 'ps_checkpayment-2'],
            $this->step->getDataToPersist()
        );
    }

    public function testRestoringDataThatCarriesNoPaymentOptionChangesNothing(): void
    {
        // The cart's checkout_session_data holds every step's keys, and a cart saved before this change
        // has no payment key at all.
        $this->step->handleRequest(['select_payment_option' => 'ps_checkpayment-2']);
        $this->step->restorePersistedData(['use_same_address' => true]);

        $this->assertSame(
            ['selected_payment_option' => 'ps_checkpayment-2'],
            $this->step->getDataToPersist()
        );
    }

    public function testNothingChosenYetPersistsNothingUsable(): void
    {
        $this->step->handleRequest([]);

        $this->assertSame(['selected_payment_option' => null], $this->step->getDataToPersist());
    }
}
