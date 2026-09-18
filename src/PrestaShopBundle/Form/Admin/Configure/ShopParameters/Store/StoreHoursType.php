<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Store;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Renders 7 text inputs (Monday–Sunday) for opening/closing hours.
 * Each value is expected in "HH:MM | HH:MM" format (e.g. "09:00 | 18:00").
 */
class StoreHoursType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $placeholder = $this->trans('e.g. 09:00 | 18:00', 'Admin.Global');

        $builder
            ->add('0', TextType::class, [
                'label' => $this->trans('Monday', 'Admin.Global'),
                'required' => false,
                'attr' => ['placeholder' => $placeholder],
            ])
            ->add('1', TextType::class, [
                'label' => $this->trans('Tuesday', 'Admin.Global'),
                'required' => false,
                'attr' => ['placeholder' => $placeholder],
            ])
            ->add('2', TextType::class, [
                'label' => $this->trans('Wednesday', 'Admin.Global'),
                'required' => false,
                'attr' => ['placeholder' => $placeholder],
            ])
            ->add('3', TextType::class, [
                'label' => $this->trans('Thursday', 'Admin.Global'),
                'required' => false,
                'attr' => ['placeholder' => $placeholder],
            ])
            ->add('4', TextType::class, [
                'label' => $this->trans('Friday', 'Admin.Global'),
                'required' => false,
                'attr' => ['placeholder' => $placeholder],
            ])
            ->add('5', TextType::class, [
                'label' => $this->trans('Saturday', 'Admin.Global'),
                'required' => false,
                'attr' => ['placeholder' => $placeholder],
            ])
            ->add('6', TextType::class, [
                'label' => $this->trans('Sunday', 'Admin.Global'),
                'required' => false,
                'attr' => ['placeholder' => $placeholder],
            ])
        ;
    }
}
