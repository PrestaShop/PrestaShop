<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Category\SEO;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\RedirectType;
use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\EventListener\TransformationFailureListener;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RedirectOptionType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly RouterInterface $router,
        private readonly DataTransformerInterface $targetTransformer,
        private readonly EventSubscriberInterface $eventSubscriber,
        private readonly int $homeCategoryId
    ) {
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [
            $this->trans('No redirection (404), display error page', 'Admin.Catalog.Feature') => RedirectType::TYPE_NOT_FOUND,
            $this->trans('No redirection (410), display error page', 'Admin.Catalog.Feature') => RedirectType::TYPE_GONE,
        ];

        if (true !== $options['isRootCategory']) {
            $choices = array_merge(
                [
                    $this->trans('Temporary redirection to a category (302)', 'Admin.Catalog.Feature') => RedirectType::TYPE_TEMPORARY,
                    $this->trans('Permanent redirection to a category (301)', 'Admin.Catalog.Feature') => RedirectType::TYPE_PERMANENT,
                ],
                $choices,
            );
        }

        $builder
            ->add('type', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'placeholder' => false, // Guaranties that no empty value is added in options
                'choices' => $choices,
                'default_empty_data' => $options['isRootCategory'] ? RedirectType::TYPE_GONE : RedirectType::TYPE_PERMANENT,
            ]);

        if (true !== $options['isRootCategory']) {
            $builder
                ->add('target', EntitySearchInputType::class, [
                    'required' => false,
                    'limit' => 1,
                    'min_length' => 3,
                    'label' => false,
                    'remote_url' => $this->router->generate('admin_categories_get_ajax_categories', ['query' => '__QUERY__']),
                    'placeholder' => $this->trans('To which category should the page redirect?', 'Admin.Catalog.Help'),
                    'help' => $this->trans('By default, the closest active parent category will be used if no category is selected.', 'Admin.Catalog.Help'),
                    'filtered_identities' => [$options['id_category'], $this->homeCategoryId],
                ]);

            // This will transform the target ID from model data into an array adapted for EntitySearchInputType
            $builder->addModelTransformer($this->targetTransformer);
            // Preset the input attributes correctly depending on the data
            $builder->addEventSubscriber($this->eventSubscriber);
        }

        // In case a transformation occurs it will be displayed as an inline error
        $builder->addEventSubscriber(new TransformationFailureListener($this->getTranslator()));
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'required' => false,
                'label' => $this->trans('Redirection when not displayed', 'Admin.Catalog.Feature'),
            ])
            ->setRequired([
                'isRootCategory',
                'id_category',
            ])
            ->setAllowedTypes('isRootCategory', 'bool')
            ->setAllowedTypes('id_category', 'int');
    }
}
