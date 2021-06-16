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

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * This form type is used for a OneToMany (or ManyToMany) association, it allows to search a list of entities
 * (based on a remote url) and associate it. It is based on the CollectionType form type which provides prototype
 * features to display a custom template for each associated items.
 *
 * A default entry type is provided with this form type @see EntityItemType which is composed of three inputs:
 *   - id
 *   - name
 *   - image
 *
 * Thus matches the default mapping of this form type via prototype_mapping, but you can change this entry type
 * to change the included data, the rendering and/or the mapping. In front the EntitySearchInput js component
 * will automatically adapt to the new mapping.
 */
class EntitySearchInputType extends CollectionType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            // These are parameters from collection type which default values are modified
            'allow_add' => true,
            'allow_delete' => true,
            'prototype_name' => '__entity_index__',

            // Default entry type that matches the default template from the prestashop ui kit form theme
            'entry_type' => EntityItemType::class,

            // This is an optional entity type that can be useful to identify which type of entity is searched
            'entity_type' => null,
            // The remote url is used internally by a javascript component which performs a request when search input is used
            'remote_url' => null,
            // Max number of selectable entities (0 is unlimited)
            'limit' => 0,
            // Search input attributes (if needed to be customized)
            'search_attr' => [],
            // List container attributes (if needed to be customized)
            'list_attr' => [],
            // Search input placeholder
            'placeholder' => '',

            // This mapping array indicate which field from the entity must be used and what placeholder use to replace
            // it (the placeholder must be used in the prototype so that the value is in the right place)
            'prototype_mapping' => [
                'id' => EntityItemType::ID_PLACEHOLDER,
                'name' => EntityItemType::NAME_PLACEHOLDER,
                'image' => EntityItemType::IMAGE_PLACEHOLDER,
            ],

            // Remove modal wording
            'remove_modal_title' => $this->trans('Delete item', 'Admin.Notifications.Warning'),
            'remove_modal_message' => $this->trans('Are you sure you want to delete this item?', 'Admin.Notifications.Warning'),
            'remove_modal_apply' => $this->trans('Delete', 'Admin.Actions'),
            'remove_modal_cancel' => $this->trans('Cancel', 'Admin.Actions'),
        ]);

        $resolver->setAllowedTypes('search_attr', ['array']);
        $resolver->setAllowedTypes('list_attr', ['array']);
        $resolver->setAllowedTypes('placeholder', ['string']);

        $resolver->setAllowedTypes('remote_url', ['string', 'null']);
        $resolver->setAllowedTypes('limit', ['int']);
        $resolver->setAllowedTypes('entity_type', ['string', 'null']);

        $resolver->setAllowedTypes('prototype_mapping', ['array']);

        $resolver->setAllowedTypes('remove_modal_title', ['string']);
        $resolver->setAllowedTypes('remove_modal_message', ['string']);
        $resolver->setAllowedTypes('remove_modal_apply', ['string']);
        $resolver->setAllowedTypes('remove_modal_cancel', ['string']);
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars = array_replace($view->vars, [
            'remote_url' => $options['remote_url'],
            'limit' => $options['limit'],
            'search_attr' => $options['search_attr'],
            'list_attr' => $options['list_attr'],
            'placeholder' => $options['placeholder'],
            'prototype_mapping' => $options['prototype_mapping'],
            'remove_modal_title' => $options['remove_modal_title'],
            'remove_modal_message' => $options['remove_modal_message'],
            'remove_modal_apply' => $options['remove_modal_apply'],
            'remove_modal_cancel' => $options['remove_modal_cancel'],
        ]);
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'entity_search_input';
    }

    /**
     * @param string $key
     * @param string $domain
     * @param array $parameters
     *
     * @return string
     */
    protected function trans(string $key, string $domain, array $parameters = []): string
    {
        return $this->translator->trans($key, $parameters, $domain);
    }
}
