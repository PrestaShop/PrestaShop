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
use Symfony\Component\OptionsResolver\Options;
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
    public const LIST_LAYOUT = 'list';
    public const TABLE_LAYOUT = 'table';

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
            'allow_search' => true,
            'prototype_name' => '__entity_index__',

            // Default entry type that matches the default template from the prestashop ui kit form theme
            'entry_type' => EntityItemType::class,
            'entry_options' => [
                // Force block prefix to easily profit from the UI kit theme (without changing it in the entity type itself)
                'block_prefix' => 'entity_item',
            ],

            // This is an optional entity type that can be useful to identify which type of entity is searched
            'entity_type' => null,
            // The remote url is used internally by a javascript component which performs a request when search input is used
            'remote_url' => null,
            // Max number of selectable entities (0 is unlimited)
            'limit' => 0,
            // Min length before suggestions start getting rendered
            'min_length' => 2,
            // Search input attributes (if needed to be customized)
            'search_attr' => [],
            // List container attributes (if needed to be customized)
            'list_attr' => [],
            // Search input placeholder
            'placeholder' => '',

            // This mapping array indicate which field from the entity must be used and what placeholder use to replace
            // it (the placeholder must be used in the prototype so that the value is in the right place)
            'prototype_mapping' => null,
            'identifier_field' => 'id',

            // Specify IDs that must be filtered out of suggestions
            'filtered_identities' => [],

            // Layout
            'layout' => static::LIST_LAYOUT,

            // Remove modal wording
            'remove_modal' => null,

            // Empty state wording
            'empty_state' => null,

            // field name in record dataset which should be used to show suggestion in search dropdown
            'suggestion_field' => 'name',
        ]);
        $resolver->setAllowedTypes('allow_search', ['bool']);
        $resolver->setAllowedTypes('search_attr', ['array']);
        $resolver->setAllowedTypes('list_attr', ['array']);
        $resolver->setAllowedTypes('placeholder', ['string']);

        $resolver->setAllowedTypes('remote_url', ['string', 'null']);
        $resolver->setAllowedTypes('limit', ['int']);
        $resolver->setAllowedTypes('min_length', ['int']);
        $resolver->setAllowedTypes('entity_type', ['string', 'null']);

        $resolver->setAllowedTypes('prototype_mapping', ['array', 'null']);
        $resolver->setAllowedTypes('identifier_field', ['string']);
        $resolver->setAllowedTypes('filtered_identities', ['array']);

        $resolver->setAllowedTypes('remove_modal', ['array', 'null']);
        $resolver->setNormalizer('remove_modal', function (Options $options, $value) {
            return $this->getRemoveModalResolver()->resolve($value ?? []);
        });

        $resolver->setAllowedTypes('layout', ['string']);
        $resolver->setAllowedValues('layout', [static::LIST_LAYOUT, static::TABLE_LAYOUT]);
        $resolver->setAllowedTypes('empty_state', ['string', 'null']);
        $resolver->setAllowedTypes('suggestion_field', ['string', 'null']);
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // If no mapping has been defined it is built based on the prototype field names
        /** @var FormInterface $prototype */
        $prototype = $form->getConfig()->getAttribute('prototype');
        if (empty($options['prototype_mapping'])) {
            $options['prototype_mapping'] = [];
            foreach ($prototype->all() as $prototypeChild) {
                $options['prototype_mapping'][$prototypeChild->getName()] = sprintf(
                    '__%s__',
                    $prototypeChild->getName()
                );
            }
        }

        // Force the data in prototype so that placeholders are injected in the prototype template then render the view
        $prototype->setData($options['prototype_mapping']);
        parent::buildView($view, $form, $options);

        // Reformat parameter name for javascript (PHP and JS don't have same naming conventions)
        $removeModal = $options['remove_modal'];
        $removeModal['buttonClass'] = $removeModal['button_class'];
        unset($removeModal['button_class']);

        $view->vars = array_replace($view->vars, [
            'allow_search' => $options['allow_search'],
            'remote_url' => $options['remote_url'],
            'limit' => $options['limit'],
            'min_length' => $options['min_length'],
            'search_attr' => $options['search_attr'],
            'list_attr' => $options['list_attr'],
            'placeholder' => $options['placeholder'],
            'prototype_mapping' => $options['prototype_mapping'],
            'remove_modal' => $removeModal,
            'list_layout' => $options['layout'],
            'empty_state' => $options['empty_state'],
            'identifier_field' => $options['identifier_field'],
            'filtered_identities' => $options['filtered_identities'],
            'suggestion_field' => $options['suggestion_field'],
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

    /**
     * @return OptionsResolver
     */
    private function getRemoveModalResolver(): OptionsResolver
    {
        $externalLinkResolver = new OptionsResolver();
        $externalLinkResolver
            ->setRequired(['title', 'message', 'apply', 'cancel', 'button_class'])
            ->setDefaults([
                'id' => 'modal-confirm-remove-entity',
                'title' => $this->trans('Delete item', 'Admin.Notifications.Warning'),
                'message' => $this->trans('Are you sure you want to delete this item?', 'Admin.Notifications.Warning'),
                'apply' => $this->trans('Delete', 'Admin.Actions'),
                'cancel' => $this->trans('Cancel', 'Admin.Actions'),
                'button_class' => 'btn-danger',
            ])
            ->setAllowedTypes('id', 'string')
            ->setAllowedTypes('title', 'string')
            ->setAllowedTypes('message', 'string')
            ->setAllowedTypes('apply', 'string')
            ->setAllowedTypes('cancel', 'string')
            ->setAllowedTypes('button_class', 'string')
        ;

        return $externalLinkResolver;
    }
}
