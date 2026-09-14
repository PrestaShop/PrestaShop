<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Search;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Form type for alias.
 */
class AliasType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alias', TextType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('active', SwitchType::class, [
                'label' => false,
                'choices' => [
                    $this->trans('Disabled', 'Admin.Global') => false,
                    $this->trans('Enabled', 'Admin.Global') => true,
                ],
                'constraints' => [
                    new NotNull(),
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'alias_type';
    }
}
