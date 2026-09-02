<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Display-only form type that shows discount usage information:
 * - "quantityUsed / totalQuantity" (e.g. "3 / 10" or "3 / ∞")
 * - "(Remaining quantity: X)" or "(Remaining quantity: ∞)"
 */
class DiscountUsagePreviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity_used', HiddenType::class)
            ->add('total_quantity', HiddenType::class)
            ->add('remaining_quantity', HiddenType::class)
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $quantityUsed = $form->get('quantity_used')->getData();
        $totalQuantity = $form->get('total_quantity')->getData();
        $remainingQuantity = $form->get('remaining_quantity')->getData();

        $view->vars['quantity_used'] = $quantityUsed ?? 0;
        $view->vars['total_quantity'] = $totalQuantity;
        $view->vars['remaining_quantity'] = $remainingQuantity;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'required' => false,
            'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Discount/FormTheme/discount_usage_preview.html.twig',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'discount_usage_preview';
    }
}
