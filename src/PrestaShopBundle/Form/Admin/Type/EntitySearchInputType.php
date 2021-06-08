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

            // Placeholders used for prototype (easy to search and replace when the entity is dynamically added by js)
            'prototype_image' => '__image__',
            'prototype_value' => '__value__',
            'prototype_display' => '__display__',

            // Mapping fields in the view data (to know which field of the js entity must be used as replacement of the placeholders)
            'mapping_value' => 'id',
            'mapping_display' => 'name',
            'mapping_image' => 'image',

            // View data used to render the selected entities (array of entities that must be displayed)
            'view_data' => null,

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

        $resolver->setAllowedTypes('prototype_image', ['string']);
        $resolver->setAllowedTypes('prototype_value', ['string']);
        $resolver->setAllowedTypes('prototype_display', ['string']);

        $resolver->setAllowedTypes('mapping_value', ['string']);
        $resolver->setAllowedTypes('mapping_display', ['string']);
        $resolver->setAllowedTypes('mapping_image', ['string']);

        $resolver->setAllowedTypes('remove_modal_title', ['string']);
        $resolver->setAllowedTypes('remove_modal_message', ['string']);
        $resolver->setAllowedTypes('remove_modal_apply', ['string']);
        $resolver->setAllowedTypes('remove_modal_cancel', ['string']);

        $resolver->setAllowedTypes('view_data', ['array', 'callable', 'null']);
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
            'prototype_image' => $options['prototype_image'],
            'prototype_value' => $options['prototype_value'],
            'prototype_display' => $options['prototype_display'],
            'mapping_image' => $options['mapping_image'],
            'mapping_value' => $options['mapping_value'],
            'mapping_display' => $options['mapping_display'],
            'remove_modal_title' => $options['remove_modal_title'],
            'remove_modal_message' => $options['remove_modal_message'],
            'remove_modal_apply' => $options['remove_modal_apply'],
            'remove_modal_cancel' => $options['remove_modal_cancel'],
        ]);

        if (is_array($options['view_data'])) {
            $view->vars['view_data'] = $options['view_data'];
        } elseif (is_callable($options['view_data'])) {
            $view->vars['view_data'] = $options['view_data']($form->getData(), $options);
        } else {
            $view->vars['view_data'] = null;
        }
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
    protected function trans(string $key, string $domain, array $parameters = [])
    {
        return $this->translator->trans($key, $parameters, $domain);
    }
}
