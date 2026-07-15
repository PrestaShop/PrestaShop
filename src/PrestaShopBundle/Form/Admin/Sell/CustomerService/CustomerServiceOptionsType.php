<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\CustomerService;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * "Contact options" panel: customer-side file upload toggle and translatable
 * default employee signature used when replying to a customer thread.
 */
final class CustomerServiceOptionsType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file_upload', SwitchType::class, [
                'label' => $this->trans('Allow file uploading', 'Admin.Catalog.Feature'),
                'help' => $this->trans(
                    'Allow customers to upload files using the contact page.',
                    'Admin.Catalog.Help'
                ),
                'required' => false,
            ])
            ->add('signature', TranslatableType::class, [
                'label' => $this->trans('Default message', 'Admin.Catalog.Feature'),
                'help' => $this->trans(
                    'Please fill out the message fields that appear by default when you answer a thread on the customer service page.',
                    'Admin.Catalog.Help'
                ),
                'required' => false,
                'type' => TextareaType::class,
                'options' => [
                    'required' => false,
                    'attr' => [
                        'rows' => 5,
                    ],
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Catalog.Feature',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'customer_service_options_block';
    }
}
