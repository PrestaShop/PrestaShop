<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition;

use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintCatalog;
use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * "Validation" card: Symfony Constraint(s) applied to the value before persistence.
 *
 * Limited to a whitelist (see ExtraPropertyConstraintMapper). Each row is one constraint: bare
 * (NotBlank), a single value via the constraint's default option (TypedRegex('generic_name')),
 * named options (Length(min: 2, max: 64)), or a composite (per_language rows fold into one
 * All[...] — the per-language validation of multilingual fields).
 *
 * The constraint rows are the MAPPED form data: the form data provider splits the stored
 * constraints into rows (ConstraintRowPresenter) and the data handler folds them back
 * (ConstraintRowSerializer -> ExtraPropertyConstraintMapper). Each row validates its own token
 * (see ExtraPropertyConstraintRowType); abandoned rows are dropped at submit by delete_empty.
 */
class ExtraPropertyDefinitionValidationType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly ExtraPropertyConstraintCatalog $constraintCatalog,
    ) {
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('constraints', CollectionType::class, [
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'error_bubbling' => false,
            'entry_type' => ExtraPropertyConstraintRowType::class,
            'entry_options' => ['label' => false],
            // per_language alone is zone bookkeeping, not content: a row is abandoned when it
            // carries neither a name nor an options tail.
            'delete_empty' => static fn (?array $row): bool => null === $row
                || ('' === trim((string) ($row['name'] ?? '')) && '' === trim((string) ($row['options'] ?? ''))),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        // Inlined by the form theme as a JSON block so the constraint builder UI knows each
        // whitelisted constraint's options without AJAX.
        $view->vars['extra_property_constraint_catalog'] = $this->constraintCatalog->getCatalog();
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => $this->trans('Validation', 'Admin.Global'),
            'icon' => 'check_circle',
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
