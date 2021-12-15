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

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Wraps SubmittableInput and DeltaQuantity components to work together.
 * admin-dev/themes/new-theme/js/components/form/submittable-delta-quantity-input.ts responsible for javascript part.
 *
 * @see DeltaQuantityType
 * @see SubmittableInputType
 */
class SubmittableDeltaQuantityType extends DeltaQuantityType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('delta', IntegerType::class, [
            'default_empty_data' => 0,
            'block_prefix' => 'submittable_delta_quantity_delta',
            'label' => false,
            'attr' => [
                'aria-label' => $this->trans('Add or subtract items', 'Admin.Global'),
            ],
            'constraints' => [
                new Type(['type' => 'numeric']),
                new NotBlank(),
            ],
        ]);
    }
}
