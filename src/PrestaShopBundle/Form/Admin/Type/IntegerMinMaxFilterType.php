<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the integer type two inputs of min and max value - designed to fit grid in grid filter.
 */
final class IntegerMinMaxFilterType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'min_field_options' => [],
            'max_field_options' => [],
        ]);

        $resolver->setAllowedTypes('min_field_options', 'array');
        $resolver->setAllowedTypes('max_field_options', 'array');
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!isset($options['min_field_options']['attr']['placeholder'])) {
            $options['min_field_options']['attr']['placeholder'] = $this->trans('Min', [], 'Admin.Global');
        }

        if (!isset($options['max_field_options']['attr']['placeholder'])) {
            $options['max_field_options']['attr']['placeholder'] = $this->trans('Max', [], 'Admin.Global');
        }

        if (!isset($options['min_field_options']['attr']['min'])) {
            $options['min_field_options']['attr']['min'] = 0;
        }

        if (!isset($options['max_field_options']['attr']['min'])) {
            $options['max_field_options']['attr']['min'] = 0;
        }

        $options['min_field_options']['attr']['step'] = 1;
        $options['max_field_options']['attr']['step'] = 1;

        $builder->add('min_field', IntegerType::class, $options['min_field_options']);
        $builder->add('max_field', IntegerType::class, $options['max_field_options']);
    }
}
