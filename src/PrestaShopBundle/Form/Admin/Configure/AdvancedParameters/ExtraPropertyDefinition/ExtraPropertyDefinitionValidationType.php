<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition;

use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintCatalog;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintMapper;
use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * "Validation" card: Symfony Constraint(s) applied to the value before persistence.
 *
 * Limited to a whitelist (see ExtraPropertyConstraintMapper). Each entry is bare (NotBlank), carries
 * a single value via the constraint's default option (TypedRegex('generic_name'), Choice(['a', 'b'])),
 * carries named options (Length(min: 2, max: 64)), or nests constraints via the composite bracket
 * shape (All[Url] — used for per-language validation of multilingual fields).
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
        $builder->add('constraints', TextareaType::class, [
            'label' => $this->trans('Constraints', 'Admin.Advparameters.Feature'),
            'help' => $this->trans("One constraint per line or comma-separated. Some accept a value, e.g. GreaterThan(5), TypedRegex('generic_name') or Choice(['a', 'b', 'c']); some accept named options, e.g. Length(min: 2, max: 64). Composites nest constraints between brackets, e.g. All[Url] to validate each language of a multilingual field. Quote string values; unquoted numbers are read as int/float and true/false/null as literals. Allowed: %names%. Leave empty to skip validation.", 'Admin.Advparameters.Help', ['%names%' => implode(', ', ExtraPropertyConstraintMapper::getAllowedNames())]),
            'required' => false,
            'attr' => ['rows' => 3],
            'constraints' => [
                new Callback([$this, 'validateConstraintNames']),
            ],
        ]);
    }

    /**
     * Surfaces a field-level error when the textarea can't be parsed, by running the exact same
     * mapper used downstream by the data handler — an unknown name, a missing required value or a
     * value on a constraint that takes none all become a form error instead of a 500.
     */
    public function validateConstraintNames(?string $value, ExecutionContextInterface $context): void
    {
        if (null === $value || '' === trim($value)) {
            return;
        }

        try {
            ExtraPropertyConstraintMapper::fromNames($value);
        } catch (ExtraPropertyException $e) {
            $context->buildViolation(
                $this->trans('Invalid constraint definition: %error%', 'Admin.Advparameters.Notification', ['%error%' => $e->getMessage()])
            )->addViolation();
        }
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
