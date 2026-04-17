<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
