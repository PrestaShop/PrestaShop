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

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Performance;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This form class generates the "Optional Features" form in Performance page.
 */
class OptionalFeaturesType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $isCombinationsUsed;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $isCombinationsUsed
     */
    public function __construct(TranslatorInterface $translator, array $locales, $isCombinationsUsed)
    {
        parent::__construct($translator, $locales);

        $this->isCombinationsUsed = $isCombinationsUsed;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('combinations', SwitchType::class, [
                'disabled' => $this->isCombinationsUsed,
                'label' => $this->trans('Combinations', 'Admin.Global'),
                'help' => sprintf(
                    '%s<br>%s',
                    $this->trans('Choose "No" to disable Product Combinations.', 'Admin.Advparameters.Help'),
                    $this->trans('You cannot set this parameter to No when combinations are already used by some of your products', 'Admin.Advparameters.Help')
                ),
            ])
            ->add('features', SwitchType::class, [
                'label' => $this->trans('Features', 'Admin.Global'),
                'help' => $this->trans('Choose "No" to disable Product Features.', 'Admin.Advparameters.Help'),
            ])
            ->add('customer_groups', SwitchType::class, [
                'label' => $this->trans('Customer groups', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Choose "No" to disable Customer Groups.', 'Admin.Advparameters.Help'),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'performance_optional_features_block';
    }
}
