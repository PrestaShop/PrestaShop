<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type is not used in the project but in the tests, it allows to build a complicated
 * form type and use it in test.
 *
 * @see FormClonerTest
 * @see FormBuilderModifierTest
 */
class AdvancedFormType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => $this->trans('Redirection when offline', 'Admin.Catalog.Feature'),
                'required' => false,
                'placeholder' => false, // Guaranties that no empty value is added in options
                'choices' => [
                    $this->trans('Default behavior from configuration', 'Admin.Catalog.Feature') => RedirectType::TYPE_DEFAULT,
                    $this->trans('No redirection (200), display product', 'Admin.Catalog.Feature') => RedirectType::TYPE_SUCCESS_DISPLAYED,
                    $this->trans('No redirection (404), display product', 'Admin.Catalog.Feature') => RedirectType::TYPE_NOT_FOUND_DISPLAYED,
                    $this->trans('No redirection (410), display product', 'Admin.Catalog.Feature') => RedirectType::TYPE_GONE_DISPLAYED,
                    $this->trans('No redirection (404), display error page', 'Admin.Catalog.Feature') => RedirectType::TYPE_NOT_FOUND,
                    $this->trans('No redirection (410), display error page', 'Admin.Catalog.Feature') => RedirectType::TYPE_GONE,
                    $this->trans('Permanent redirection to a category (301)', 'Admin.Catalog.Feature') => RedirectType::TYPE_CATEGORY_PERMANENT,
                    $this->trans('Temporary redirection to a category (302)', 'Admin.Catalog.Feature') => RedirectType::TYPE_CATEGORY_TEMPORARY,
                    $this->trans('Permanent redirection to a product (301)', 'Admin.Catalog.Feature') => RedirectType::TYPE_PRODUCT_PERMANENT,
                    $this->trans('Temporary redirection to a product (302)', 'Admin.Catalog.Feature') => RedirectType::TYPE_PRODUCT_TEMPORARY,
                ],
            ])
            ->add('target', ChoiceType::class, [
                'required' => false,
                'error_bubbling' => false,
            ])
        ;

        if ($options['add_model_transformer'] instanceof DataTransformerInterface) {
            $builder->get('target')->addModelTransformer($options['add_model_transformer']);
            $builder->addModelTransformer($options['add_model_transformer']);
        }

        if ($options['add_view_transformer'] instanceof DataTransformerInterface) {
            $builder->get('target')->addViewTransformer($options['add_view_transformer']);
            $builder->addViewTransformer($options['add_view_transformer']);
        }

        if (isset($options['add_event_subscriber'])) {
            $builder->addEventSubscriber($options['add_event_subscriber']);
        }

        if (!empty($options['add_event_listener'])) {
            $builder->addEventListener($options['add_event_listener']['event'], $options['add_event_listener']['callback']);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'add_event_subscriber' => null,
            'add_event_listener' => null,
            'add_model_transformer' => null,
            'add_view_transformer' => null,
        ]);
        $resolver->setAllowedTypes('add_event_subscriber', [EventSubscriberInterface::class, 'null']);
        $resolver->setAllowedTypes('add_event_listener', ['array', 'null']);
        $resolver->setAllowedTypes('add_model_transformer', [DataTransformerInterface::class, 'null']);
        $resolver->setAllowedTypes('add_view_transformer', [DataTransformerInterface::class, 'null']);
    }
}
