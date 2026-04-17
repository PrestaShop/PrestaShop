<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Pricing;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceSummaryType extends TranslatorAwareType
{
    public function configureOptions(OptionsResolver $resolver)
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
