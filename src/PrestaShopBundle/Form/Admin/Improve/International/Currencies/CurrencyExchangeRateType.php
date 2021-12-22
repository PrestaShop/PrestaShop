<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Currencies;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Service\Routing\Router;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class CurrencyExchangeRateType
 */
class CurrencyExchangeRateType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $isCronModuleInstalled;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $isCronModuleInstalled
     * @param Router $router
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        bool $isCronModuleInstalled,
        Router $router
    ) {
        parent::__construct($translator, $locales);
        $this->isCronModuleInstalled = $isCronModuleInstalled;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('live_exchange_rate', SwitchType::class, [
                'disabled' => !$this->isCronModuleInstalled,
                'label' => $this->trans('Live exchange rates', 'Admin.International.Feature'),
                'attr' => [
                    'class' => 'js-live-exchange-rate',
                    'data-url' => $this->router->generate('admin_currencies_update_live_exchange_rates'),
                ],
            ])
        ;
    }
}
