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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Pricing;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceSummaryType extends TranslatorAwareType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => $this->trans('Summary', 'Admin.Global'),
            'label_tag_name' => 'h3',
            'attr' => [
                'class' => 'price-summary-widget form-group',
                'data-price-tax-excluded' => $this->trans('%price% tax excl.', 'Admin.Catalog.Feature'),
                'data-price-tax-included' => $this->trans('%price% tax incl.', 'Admin.Catalog.Feature'),
                'data-unit-price' => $this->trans('%price% %unity%', 'Admin.Catalog.Feature'),
                'data-margin' => $this->trans('%price% margin', 'Admin.Catalog.Feature'),
                'data-margin-rate' => $this->trans('%margin_rate%% margin rate', 'Admin.Catalog.Feature'),
                'data-wholesale-price' => $this->trans('%price% cost price', 'Admin.Catalog.Feature'),
            ],
        ]);
    }
}
