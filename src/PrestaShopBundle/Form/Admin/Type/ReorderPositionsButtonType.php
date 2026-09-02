<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ReorderPositionsButtonType.
 */
class ReorderPositionsButtonType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('position', ButtonType::class, [
            'label' => $this->trans('Rearrange', 'Admin.Actions'),
            'row_attr' => ['class' => 'mb-0'],
            'attr' => [
                'class' => 'btn-default js-btn-reorder-positions',
                'data-label-reorder' => $this->trans('Rearrange', 'Admin.Actions'),
                'data-label-save' => $this->trans('Done', 'Admin.Actions'),
            ],
        ]);
    }
}
