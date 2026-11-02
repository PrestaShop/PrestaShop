<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form;

use Module;
use PrestaShop\PrestaShop\Adapter\TradeCredit\Repository\TradeCreditPaymentModuleRepository;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class PaymentPreferencesTradeCreditTest extends KernelTestCase
{
    private FormHandlerInterface $formHandler;

    private TradeCreditPaymentModuleRepository $repository;

    private int $bankwireId;

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

        $this->formHandler = self::getContainer()->get('prestashop.admin.payment_preferences.form_handler');
        $this->repository = self::getContainer()->get(TradeCreditPaymentModuleRepository::class);
        $this->bankwireId = (int) Module::getModuleIdByName('bankwire');
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['module_trade_credit']);
        parent::tearDown();
    }

    public function testFormOffersPaymentModulesForTradeCredit(): void
    {
        $form = $this->formHandler->getForm();

        $this->assertTrue($form->has('trade_credit_modules'));
        $this->assertContains('bankwire', $form->get('trade_credit_modules')->getConfig()->getOption('choices'));
    }

    public function testSavingFormFlagsSelectedModule(): void
    {
        $data = $this->formHandler->getForm()->getData();
        $data['trade_credit_modules'] = ['bankwire'];

        $this->assertSame([], $this->formHandler->save($data));

        $this->assertTrue($this->repository->isTradeCreditModule($this->bankwireId, 1));
        $this->assertSame(['bankwire'], $this->formHandler->getForm()->get('trade_credit_modules')->getData());
    }

    public function testSavingFormWithoutSelectionClearsFlag(): void
    {
        $this->repository->setTradeCreditModules(1, [$this->bankwireId]);
        $data = $this->formHandler->getForm()->getData();
        $data['trade_credit_modules'] = [];

        $this->assertSame([], $this->formHandler->save($data));

        $this->assertFalse($this->repository->isTradeCreditModule($this->bankwireId, 1));
    }
}
