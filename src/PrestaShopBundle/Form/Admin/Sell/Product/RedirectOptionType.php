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

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Admin\Type\TypeaheadProductCollectionType;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\EventListener\TransformationFailureListener;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class RedirectOptionType extends TranslatorAwareType
{
    /**
     * @var LegacyContext
     */
    private $context;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var DataTransformerInterface
     */
    private $targetTransformer;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param LegacyContext $context
     * @param RouterInterface $router
     * @param DataTransformerInterface $targetTransformer
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        LegacyContext $context,
        RouterInterface $router,
        DataTransformerInterface $targetTransformer
    ) {
        parent::__construct($translator, $locales);
        $this->context = $context;
        $this->router = $router;
        $this->targetTransformer = $targetTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entityAttributes = [
            'product' => [
                'label' => $this->trans('Target product', 'Admin.Catalog.Feature'),
                'placeholder' => $this->trans('To which product the page should redirect?', 'Admin.Catalog.Help'),
                'help' => '',
                'searchUrl' => $this->context->getLegacyAdminLink('AdminProducts', true, ['ajax' => 1, 'action' => 'productsList', 'forceJson' => 1, 'disableCombination' => 1, 'exclude_packs' => 0, 'excludeVirtuals' => 0, 'limit' => 20]) . '&q=__QUERY__',
            ],
            'category' => [
                'label' => $this->trans('Target category', 'Admin.Catalog.Feature'),
                'placeholder' => $this->trans('To which category the page should redirect?', 'Admin.Catalog.Help'),
                'help' => $this->trans('If no category is selected the Main Category is used', 'Admin.Catalog.Help'),
                'searchUrl' => $this->router->generate('admin_get_ajax_categories', ['query' => '__QUERY__']),
            ],
        ];
        $defaultEntity = 'product';

        $builder
            ->add('type', ChoiceType::class, [
                'label' => $this->trans('Redirection when offline', 'Admin.Catalog.Feature'),
                'required' => false,
                'placeholder' => false, // Guaranties that no empty value is added in options
                'choices' => [
                    $this->trans('No redirection (404)', 'Admin.Catalog.Feature') => RedirectType::TYPE_NOT_FOUND,
                    $this->trans('Permanent redirection to a category (301)', 'Admin.Catalog.Feature') => RedirectType::TYPE_CATEGORY_PERMANENT,
                    $this->trans('Temporary redirection to a category (302)', 'Admin.Catalog.Feature') => RedirectType::TYPE_CATEGORY_TEMPORARY,
                    $this->trans('Permanent redirection to a product (301)', 'Admin.Catalog.Feature') => RedirectType::TYPE_PRODUCT_PERMANENT,
                    $this->trans('Temporary redirection to a product (302)', 'Admin.Catalog.Feature') => RedirectType::TYPE_PRODUCT_TEMPORARY,
                ],
            ])
            ->add('target', TypeaheadProductCollectionType::class, [
                'required' => false,
                'error_bubbling' => false,
                'template_collection' => '<span class="label">%s</span>',
                'limit' => 1,
                'label' => $entityAttributes[$defaultEntity]['label'],
                'remote_url' => $entityAttributes[$defaultEntity]['searchUrl'],
                'placeholder' => $entityAttributes[$defaultEntity]['placeholder'],
                'help' => $entityAttributes[$defaultEntity]['help'],
                'attr' => [
                    'data-product-label' => $entityAttributes['product']['label'],
                    'data-product-placeholder' => $entityAttributes['product']['placeholder'],
                    'data-product-search-url' => $entityAttributes['product']['searchUrl'],
                    'data-category-label' => $entityAttributes['category']['label'],
                    'data-category-placeholder' => $entityAttributes['category']['placeholder'],
                    'data-category-hint' => $entityAttributes['category']['help'],
                    'data-category-search-url' => $entityAttributes['category']['searchUrl'],
                ],
            ])
        ;

        // This will transform the target ID from model data into an array adapted for TypeaheadProductCollectionType
        $builder->get('target')->addModelTransformer($this->targetTransformer);
        // In case a transformation occurs it will be displayed as an inline error
        $builder->addEventSubscriber(new TransformationFailureListener($this->getTranslator()));

        // Preset the input attributes correctly depending on the data
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($entityAttributes) {
            $data = $event->getData();
            $form = $event->getForm();
            $targetField = $form->get('target');
            $targetOptions = $targetField->getConfig()->getOptions();
            $dataType = $data['type'] ?? RedirectType::TYPE_NOT_FOUND;
            switch ($dataType) {
                case RedirectType::TYPE_CATEGORY_PERMANENT:
                case RedirectType::TYPE_CATEGORY_TEMPORARY:
                    $dataEntity = 'category';
                    break;
                case RedirectType::TYPE_PRODUCT_PERMANENT:
                case RedirectType::TYPE_PRODUCT_TEMPORARY:
                default:
                    $dataEntity = 'product';
                    break;
            }

            // Adapt target options
            $targetOptions['mapping_type'] = $dataEntity;
            $targetOptions['label'] = $entityAttributes[$dataEntity]['label'];
            $targetOptions['placeholder'] = $entityAttributes[$dataEntity]['placeholder'];
            $targetOptions['help'] = $entityAttributes[$dataEntity]['help'];
            $targetOptions['remote_url'] = $entityAttributes[$dataEntity]['searchUrl'];

            // Replace existing field with new one with adapted options
            $cloner = new FormCloner();
            $clonedForm = $cloner->cloneForm($targetField, $targetOptions);
            $form->add($clonedForm);
        });
    }
}
