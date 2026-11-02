<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use PrestaShop\PrestaShop\Adapter\ContainerFinder;
use PrestaShop\PrestaShop\Adapter\TradeCredit\TradeCreditOrderService;
use PrestaShop\PrestaShop\Core\Exception\ContainerNotFoundException;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
use PrestaShop\PrestaShop\Core\Payment\PaymentOptionFormDecorator;
use PrestaShopBundle\Service\Hook\HookFinder;

class PaymentOptionsFinderCore extends HookFinder
{
    /**
     * Collects available payment options from three different hooks.
     *
     * @return array An array of available payment options
     *
     * @see HookFinder::find()
     */
    public function find() // getPaymentOptions()
    {
        // Payment options coming from intermediate, deprecated version of the Advanced API
        $this->hookName = 'displayPaymentEU';
        $rawDisplayPaymentEUOptions = parent::find();
        $paymentOptions = array_map(
            ['PrestaShop\PrestaShop\Core\Payment\PaymentOption', 'convertLegacyOption'],
            $rawDisplayPaymentEUOptions
        );

        // Advanced payment options coming from regular Advanced API
        $this->hookName = 'advancedPaymentOptions';
        $paymentOptions = array_merge($paymentOptions, parent::find());

        // Payment options coming from regular Advanced API
        $this->hookName = 'paymentOptions';
        $this->expectedInstanceClasses = ['PrestaShop\PrestaShop\Core\Payment\PaymentOption'];
        $paymentOptions = array_merge($paymentOptions, parent::find());

        // Safety check
        foreach ($paymentOptions as $moduleName => $paymentOption) {
            if (!is_array($paymentOption) || empty($paymentOption)) {
                unset($paymentOptions[$moduleName]);
            }
        }

        return $paymentOptions;
    }

    public function findFree()
    {
        $freeOption = new PaymentOption();
        $freeOption->setModuleName('free_order')
            ->setCallToActionText(Context::getContext()->getTranslator()->trans('Free order', [], 'Admin.Orderscustomers.Feature'))
            ->setAction(Context::getContext()->link->getPageLink('order-confirmation', null, null, 'free_order=1'));

        return ['free_order' => [$freeOption]];
    }

    /**
     * Shows the available trade credit on trade credit payment options and marks the ones the checkout
     * javascript must disable (the order is also rejected server side).
     */
    private function presentTradeCredit(array $paymentOptions): array
    {
        $context = Context::getContext();
        if (!Validate::isLoadedObject($context->cart) || !Validate::isLoadedObject($context->customer)) {
            return $paymentOptions;
        }

        try {
            $tradeCreditService = (new ContainerFinder($context))->getContainer()->get(TradeCreditOrderService::class);
        } catch (ContainerNotFoundException) {
            return $paymentOptions;
        }

        return $tradeCreditService->decoratePaymentOptions($paymentOptions, $context->cart, $context->customer, $context->currency, (int) $context->shop->id);
    }

    public function present($free = false) // getPaymentOptionsForTemplate()
    {
        $id = 0;

        $find = $free ? $this->findFree() : $this->find();

        $paymentOptions = array_map(function (array $options) use (&$id) {
            return array_map(function (PaymentOption $option) use (&$id) {
                ++$id;
                $formattedOption = $option->toArray();
                $formattedOption['id'] = 'payment-option-' . $id;

                if ($formattedOption['form']) {
                    $decorator = new PaymentOptionFormDecorator();
                    $formattedOption['form'] = $decorator->addHiddenSubmitButton(
                        $formattedOption['form'],
                        $formattedOption['id']
                    );
                }

                return $formattedOption;
            }, $options);
        }, $find);

        if (!$free) {
            $paymentOptions = $this->presentTradeCredit($paymentOptions);
        }

        Hook::exec('actionPresentPaymentOptions',
            ['paymentOptions' => &$paymentOptions]
        );

        return $paymentOptions;
    }
}
