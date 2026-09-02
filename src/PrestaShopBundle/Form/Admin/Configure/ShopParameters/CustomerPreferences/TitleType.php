<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\CustomerPreferences;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\Gender;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class generates "General" form
 * in "Configure > Shop Parameters > Title" page.
 */
class TitleType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class, [
                'constraints' => [new DefaultLanguage()],
                'label' => $this->trans(
                    'Title',
                    'Admin.Global'
                ),
            ])
            ->add('gender_type', ChoiceType::class, [
                'label' => $this->trans(
                    'Gender',
                    'Admin.Global'
                ),
                'expanded' => true,
                'placeholder' => false,
                'choices' => [
                    $this->trans('Male', 'Admin.Shopparameters.Feature') => Gender::TYPE_MALE,
                    $this->trans('Female', 'Admin.Shopparameters.Feature') => Gender::TYPE_FEMALE,
                    $this->trans('Neutral', 'Admin.Shopparameters.Feature') => Gender::TYPE_OTHER,
                ],
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'label' => $this->trans(
                    'Image',
                    'Admin.Global'
                ),
                'required' => false,
            ])
            ->add('img_width', IntegerType::class, [
                'label' => $this->trans(
                    'Image width',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'Image width in pixels. Enter "0" to use the original size.',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
            ])
            ->add('img_height', IntegerType::class, [
                'label' => $this->trans(
                    'Image height',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'Image height in pixels. Enter "0" to use the original size.',
                    'Admin.Shopparameters.Help'
                ),
                'required' => false,
            ])
        ;
    }
}
