<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Default entry type used by @see EntitySearchInputType
 */
class EntityItemType extends CommonAbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class, [
                'label' => false,
            ])
            ->add('name', TextPreviewType::class, [
                'label' => false,
            ])
            ->add('image', ImagePreviewType::class, [
                'label' => false,
            ])
        ;
    }
}
