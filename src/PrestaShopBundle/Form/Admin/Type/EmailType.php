<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Form\DataTransformer\IDNConverterDataTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType as BaseEmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailType extends BaseEmailType
{
    public function __construct(
        protected readonly IDNConverterDataTransformer $IDNConverterDataTransformer,
        protected readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer($this->IDNConverterDataTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'attr' => [
                'class' => 'email-input',
                'data-invalid-message' => $this->translator->trans('Invalid email address.', [], 'Admin.Notifications.Error'),
            ],
        ]);
    }
}
