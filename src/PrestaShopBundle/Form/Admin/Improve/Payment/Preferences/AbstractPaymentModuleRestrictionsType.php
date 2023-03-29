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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\Payment\Preferences;

use PrestaShop\PrestaShop\Core\Module\ModuleInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Defines is abstract for all restriction forms in Payment Preferences
 */
abstract class AbstractPaymentModuleRestrictionsType extends TranslatorAwareType
{
    /**
     * @var array
     */
    protected $paymentModules;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array<string, ModuleInterface> $paymentModules
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $paymentModules
    ) {
        parent::__construct($translator, $locales);

        $this->paymentModules = $this->sortPaymentModules($paymentModules);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/prestashop_ui_kit.html.twig',
        ]);
    }

    /**
     * Sort payment modules by display name.
     *
     * @param array $paymentModules
     *
     * @return array
     */
    protected function sortPaymentModules(array $paymentModules): array
    {
        $sortingBy = [];

        foreach ($paymentModules as $key => $paymentModule) {
            $sortingBy[$key] = $paymentModule->get('displayName');
        }

        array_multisort($sortingBy, $paymentModules);

        return $paymentModules;
    }
}
