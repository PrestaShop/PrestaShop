<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form;

use DateTimeImmutable;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Exception\TradeCreditBalanceChangedException;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\QueryResult\CustomerTradeCreditSummary;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\QueryResult\TradeCreditOperation;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use PrestaShopBundle\Entity\Repository\CustomerTradeCreditRepository;
use PrestaShopBundle\Form\Admin\Sell\Customer\CustomerType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * Customer form data provider + data handler round trip for the trade credit fields.
 */
class CustomerFormTradeCreditTest extends KernelTestCase
{
    private const CUSTOMER_ID = 1;

    private FormDataProviderInterface $dataProvider;

    private FormDataHandlerInterface $dataHandler;

    private CustomerTradeCreditRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var ShopContextBuilder $shopContextBuilder */
        $shopContextBuilder = self::getContainer()->get('test_shop_context_builder');
        $shopContextBuilder->setShopId(1);
        $shopContextBuilder->setShopConstraint(ShopConstraint::shop(1));
        /** @var LanguageContextBuilder $languageContextBuilder */
        $languageContextBuilder = self::getContainer()->get('test_language_context_builder');
        $languageContextBuilder->setLanguageId(1);

        $this->dataProvider = self::getContainer()->get('prestashop.core.form.identifiable_object.data_provider.customer_form_data_provider');
        $this->dataHandler = self::getContainer()->get('prestashop.core.form.identifiable_object.data_handler.customer_form_data_handler');
        $this->repository = self::getContainer()->get(CustomerTradeCreditRepository::class);
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['customer', 'customer_group', 'customer_trade_credit', 'customer_trade_credit_operation']);
        parent::tearDown();
    }

    public function testSavingChangedAmountDispatchesAdjustment(): void
    {
        $data = $this->formData();
        $this->assertSame('50000.00', $data['trade_credit_expected_amount']);
        $data['trade_credit_amount'] = '70000';

        $this->dataHandler->update(self::CUSTOMER_ID, $data);

        $this->assertSame('70000.000000', $this->repository->findBalance(self::CUSTOMER_ID));
        $this->assertSame('70000.00', $this->dataProvider->getData(self::CUSTOMER_ID)['trade_credit_amount']);
    }

    public function testSavingUnchangedAmountCreatesNothing(): void
    {
        $data = $this->formData();

        $this->dataHandler->update(self::CUSTOMER_ID, $data);

        $this->assertNull($this->repository->findBalance(self::CUSTOMER_ID));
    }

    public function testStaleExpectedAmountIsRejected(): void
    {
        $data = $this->formData();
        $this->repository->createBalance(self::CUSTOMER_ID, '40000.00');
        $data['trade_credit_amount'] = '60000';

        try {
            $this->dataHandler->update(self::CUSTOMER_ID, $data);
            $this->fail('Expected the stale trade credit form to be rejected.');
        } catch (TradeCreditBalanceChangedException) {
        }

        $this->assertSame('40000.000000', $this->repository->findBalance(self::CUSTOMER_ID));
    }

    public function testEditFormHasTradeCreditFields(): void
    {
        $form = self::getContainer()->get('prestashop.core.form.identifiable_object.builder.customer_form_builder')
            ->getFormFor(self::CUSTOMER_ID, [], ['is_password_required' => false, 'show_guest_field' => false]);

        $this->assertTrue($form->has('trade_credit_amount'));
        $this->assertSame('50000.00', $form->get('trade_credit_expected_amount')->getData());
    }

    public function testGuestFormHasNoTradeCreditFields(): void
    {
        $form = self::getContainer()->get('form.factory')->create(CustomerType::class, ['is_guest' => true]);

        $this->assertFalse($form->has('trade_credit_amount'));
        $this->assertFalse($form->has('trade_credit_expected_amount'));
    }

    public function testTradeCreditCardRendersBalanceAndHistory(): void
    {
        $summary = new CustomerTradeCreditSummary(
            new DecimalNumber('39000'),
            new DecimalNumber('50000'),
            'USD',
            [new TradeCreditOperation(new DateTimeImmutable(), 'debit', new DecimalNumber('-11000'), new DecimalNumber('39000'), 5)]
        );

        $html = self::getContainer()->get('twig')->render(
            '@PrestaShop/Admin/Sell/Customer/Blocks/View/trade_credit.html.twig',
            ['customerTradeCredit' => $summary]
        );

        $this->assertStringContainsString('customer-trade-credit-card', $html);
        $this->assertStringContainsString('39,000.00', $html);
        $this->assertStringContainsString('#5', $html);
    }

    /**
     * Form data as loaded in the edit form; the password field is submitted empty when unchanged.
     */
    private function formData(): array
    {
        return array_merge($this->dataProvider->getData(self::CUSTOMER_ID), ['password' => null]);
    }
}
