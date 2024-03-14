<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
            $this->trans('No redirection (410), display error page', 'Admin.Catalog.Feature') => RedirectType::TYPE_GONE,
            $this->trans('No redirection (404), display error page', 'Admin.Catalog.Feature') => RedirectType::TYPE_NOT_FOUND,
        ];

        if (true !== $options['isRootCategory']) {
            $choices = array_merge(
                [
                    $this->trans('Permanent redirection to a category (301)', 'Admin.Catalog.Feature') => RedirectType::TYPE_PERMANENT,
                    $this->trans('Temporary redirection to a category (302)', 'Admin.Catalog.Feature') => RedirectType::TYPE_TEMPORARY,
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
            ]);

        if (true !== $options['isRootCategory']) {
            $builder
                ->add('target', EntitySearchInputType::class, [
                    'required' => false,
                    'limit' => 1,
                    'min_length' => 3,
                    'attr' => [
                        'data-label' => $this->trans('Target category', 'Admin.Catalog.Feature'),
                        'data-placeholder' => $this->trans('To which category should the page redirect?', 'Admin.Catalog.Help'),
                        'data-search-url' => $this->router->generate('admin_categories_get_ajax_categories', ['query' => '__QUERY__']),
                        'data-help' => $this->trans('By default, the main category will be used if no category is selected.', 'Admin.Catalog.Help'),
                        'data-filtered' => json_encode([$this->homeCategoryId]),
                    ],
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
                'label' => $this->trans('Redirection when offline', 'Admin.Catalog.Feature'),
            ])
            ->setRequired([
                'isRootCategory',
            ])
            ->setAllowedTypes('isRootCategory', 'bool');
    }
}
