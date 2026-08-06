<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition;

use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\ApiEndpointCatalog;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\FormCatalog;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\GridCatalog;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertyFormTypeMap;
use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * "Placement" card: where the field appears in the back office (forms, grids) and Admin API,
 * plus the developer-oriented form type/options overrides.
 *
 * Each association is a MAPPED collection of builder rows — the submitted form data itself. The
 * form data provider splits the stored entries into rows (AssociationRowPresenter) and the data
 * handler serializes them back (AssociationRowSerializer). Each row validates its own grammar
 * (see the row types); the collections only add the cross-row rule the value object enforces too:
 * a form/grid may only be referenced once. Abandoned rows (all fields empty) are dropped at
 * submit by delete_empty.
 *
 * The card exposes the forms/grids/APIs catalogs and the type=>default form type map as the
 * "extra_property_catalogs" view var, inlined by the form theme as a JSON block so the picker
 * components can suggest ids without AJAX (the per-form field tree stays lazy — see
 * ExtraPropertyDefinitionController::formFieldsAction()).
 *
 * Defined in app/config/admin/services.yml ONLY (excluded from the form types prototype glob):
 * ApiEndpointCatalog needs the OpenApi services that exist solely in the admin kernel (see its
 * docblock). Building this form in another kernel fails until swagger is enabled there.
 */
class ExtraPropertyDefinitionAdvancedType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly FormCatalog $formCatalog,
        private readonly GridCatalog $gridCatalog,
        private readonly ApiEndpointCatalog $apiEndpointCatalog,
        private readonly ExtraPropertyFormTypeMap $formTypeMap,
    ) {
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('form_type', TextType::class, [
                'label' => $this->trans('Symfony form type', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Fully-qualified Symfony form type class name to override the default type mapping (e.g. Symfony\Component\Form\Extension\Core\Type\UrlType).', 'Admin.Advparameters.Help'),
                'required' => false,
            ])
            ->add('form_options', TextareaType::class, [
                'label' => $this->trans('Form options', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Extra options merged into the Symfony form type constructor, as a JSON object. Example: {"attr": {"class": "my-class"}, "row_attr": {"data-toggle": "tooltip"}}', 'Admin.Advparameters.Help'),
                'required' => false,
                'attr' => ['rows' => 3],
                'constraints' => [
                    new Json(['message' => $this->trans('The form options must be a valid JSON object.', 'Admin.Advparameters.Notification')]),
                ],
            ])
            ->add('associated_forms', CollectionType::class, array_merge($this->rowCollectionOptions(), [
                'entry_type' => ExtraPropertyFormPlacementRowType::class,
                'constraints' => [new Callback($this->uniqueIdValidator('form_id', $this->trans('Duplicate form "%id%" — each form may only be referenced once.', 'Admin.Advparameters.Notification')))],
            ]))
            ->add('associated_grids', CollectionType::class, array_merge($this->rowCollectionOptions(), [
                'entry_type' => ExtraPropertyGridPlacementRowType::class,
                'constraints' => [new Callback($this->uniqueIdValidator('grid_id', $this->trans('Duplicate grid "%id%" — each grid may only be referenced once.', 'Admin.Advparameters.Notification')))],
            ]))
            ->add('associated_apis', CollectionType::class, array_merge($this->rowCollectionOptions(), [
                'entry_type' => ExtraPropertyApiPlacementRowType::class,
            ]));
    }

    /**
     * Common options of the placement-row collections: mapped form data, rows freely added and
     * removed client-side against the standard data-prototype, abandoned rows dropped at submit.
     *
     * @return array<string, mixed>
     */
    private function rowCollectionOptions(): array
    {
        return [
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            // Keep collection-level violations (duplicate ids) ON the collection.
            'error_bubbling' => false,
            'entry_options' => ['label' => false],
            'delete_empty' => static fn (?array $row): bool => null === $row
                || [] === array_filter($row, static fn (?string $value): bool => '' !== trim((string) $value)),
        ];
    }

    /**
     * The collection-level rule a single row cannot check: a form/grid may only be referenced once
     * across the rows (the same uniqueness the ExtraPropertyDefinition constructor enforces). The
     * violation lands on the duplicate row's id field.
     *
     * @return callable(list<array<string, string|null>>|null, ExecutionContextInterface): void
     */
    private function uniqueIdValidator(string $idField, string $message): callable
    {
        return static function (?array $rows, ExecutionContextInterface $context) use ($idField, $message): void {
            $seen = [];
            foreach ((array) $rows as $index => $row) {
                $id = trim((string) ($row[$idField] ?? ''));
                if ('' === $id) {
                    continue;
                }
                if (isset($seen[$id])) {
                    $context->buildViolation($message)
                        ->setParameter('%id%', $id)
                        ->atPath(sprintf('[%d][%s]', $index, $idField))
                        ->addViolation();
                    continue;
                }
                $seen[$id] = true;
            }
        };
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['extra_property_catalogs'] = [
            'forms' => $this->formCatalog->getAll(),
            'grids' => $this->gridCatalog->getAll(),
            'apis' => $this->apiEndpointCatalog->getAll(),
            'defaultFormTypes' => $this->formTypeMap->getMap(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => $this->trans('Placement', 'Admin.Advparameters.Feature'),
            'label_subtitle' => $this->trans('Where this field appears in the back office and Admin API.', 'Admin.Advparameters.Help'),
            'icon' => 'place_item',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return CardType::class;
    }
}
