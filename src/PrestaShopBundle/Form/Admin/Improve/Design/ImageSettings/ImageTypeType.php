<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\Design\ImageSettings;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class ImageTypeType extends TranslatorAwareType
{
    /** Minimum size for width and height in pixels of the image type */
    private const MIN_PX_SIZE = 1;

    /** Maximum size for width and height in pixels for the image type */
    private const MAX_PX_SIZE = 9999;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->trans('Name for the image type', 'Admin.Design.Feature'),
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_IMAGE_TYPE_NAME,
                    ]),
                ],
                'help' => $this->trans('Letters, underscores and hyphens only (e.g. "small_custom", "cart_medium", "large", "thickbox_extra-large").', 'Admin.Design.Help'),
            ])
            ->add('width', IntegerType::class, [
                'label' => $this->trans('Width', 'Admin.Global'),
                'attr' => [
                    'min' => self::MIN_PX_SIZE,
                    'max' => self::MAX_PX_SIZE,
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Range([
                        'min' => self::MIN_PX_SIZE,
                        'max' => self::MAX_PX_SIZE,
                        'notInRangeMessage' => $this->trans(
                            'This field must be between %min% and %max%',
                            'Admin.Notifications.Error',
                            ['%min%' => self::MIN_PX_SIZE, '%max%' => self::MAX_PX_SIZE]
                        ),
                    ]),
                ],
                'help' => $this->trans('Maximum image width in pixels.', 'Admin.Design.Help'),
            ])
            ->add('height', IntegerType::class, [
                'label' => $this->trans('Height', 'Admin.Global'),
                'attr' => [
                    'min' => self::MIN_PX_SIZE,
                    'max' => self::MAX_PX_SIZE,
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Range([
                        'min' => self::MIN_PX_SIZE,
                        'max' => self::MAX_PX_SIZE,
                        'notInRangeMessage' => $this->trans(
                            'This field must be between %min% and %max%',
                            'Admin.Notifications.Error',
                            ['%min%' => self::MIN_PX_SIZE, '%max%' => self::MAX_PX_SIZE]
                        ),
                    ]),
                ],
                'help' => $this->trans('Maximum image height in pixels.', 'Admin.Design.Help'),
            ])
            ->add('products', SwitchType::class, [
                'label' => $this->trans('Products', 'Admin.Global'),
                'help' => $this->trans('This type will be used for Product images.', 'Admin.Design.Help'),
                'required' => false,
            ])
            ->add('categories', SwitchType::class, [
                'label' => $this->trans('Categories', 'Admin.Global'),
                'help' => $this->trans('This type will be used for Category images.', 'Admin.Design.Help'),
                'required' => false,
            ])
            ->add('manufacturers', SwitchType::class, [
                'label' => $this->trans('Brands', 'Admin.Global'),
                'help' => $this->trans('This type will be used for Brand images.', 'Admin.Design.Help'),
                'required' => false,
            ])
            ->add('suppliers', SwitchType::class, [
                'label' => $this->trans('Suppliers', 'Admin.Global'),
                'help' => $this->trans('This type will be used for Supplier images.', 'Admin.Design.Help'),
                'required' => false,
            ])
            ->add('stores', SwitchType::class, [
                'label' => $this->trans('Stores', 'Admin.Global'),
                'help' => $this->trans('This type will be used for Store images.', 'Admin.Design.Help'),
                'required' => false,
            ])
        ;
    }
}
