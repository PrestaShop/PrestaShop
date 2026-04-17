<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\Design\ImageSettings;

use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\ImageTypeChoiceProvider;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegenerateThumbnailsType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly ImageTypeChoiceProvider $imageTypeChoiceProvider
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('image', ChoiceType::class, [
                'label' => $this->trans('Select an image', 'Admin.Design.Feature'),
                'attr' => [
                    'data-formats' => json_encode($this->imageTypeChoiceProvider->buildChoicesByTypes()),
                ],
                'constraints' => [
                    new NotBlank(),
                ],
                'choices' => [
                    $this->trans('All', 'Admin.Global') => 'all',
                    $this->trans('Categories', 'Admin.Global') => 'categories',
                    $this->trans('Brands', 'Admin.Global') => 'manufacturers',
                    $this->trans('Suppliers', 'Admin.Global') => 'suppliers',
                    $this->trans('Products', 'Admin.Global') => 'products',
                    $this->trans('Stores', 'Admin.Global') => 'stores',
                ],
            ])
            ->add('image-type', ChoiceType::class, [
                'label' => $this->trans('Select a format', 'Admin.Design.Feature'),
                'constraints' => [
                    new NotBlank(),
                ],
                'choices' => [
                    $this->trans('All', 'Admin.Global') => 0,
                    ...$this->imageTypeChoiceProvider->getChoices(),
                ],
            ])
            ->add('erase-previous-images', SwitchType::class, [
                'label' => $this->trans('Erase previous images', 'Admin.Design.Feature'),
                'help' => $this->trans('Select "No" only if your server timed out and you need to resume the regeneration.', 'Admin.Design.Help'),
                'required' => false,
                'data' => false,
            ])
        ;
    }
}
