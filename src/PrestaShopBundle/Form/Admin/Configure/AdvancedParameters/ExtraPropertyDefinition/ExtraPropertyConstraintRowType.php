<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition;

use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ConstraintRowSerializer;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintMapper;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * One row of the Validation card's constraint builder — a MAPPED collection entry carrying one
 * constraint DSL token. The data handler folds the rows back into the DSL string through
 * ConstraintRowSerializer.
 *
 * options holds the token's VERBATIM argument tail (the text between "(...)"/"[...]" — e.g.
 * "min: 2, max: 64" or "'generic_name'"); the page JS renders typed inputs over it when it can and
 * shows it as-is when it can't, so the row stays lossless either way. per_language flags the rows
 * living in the "Applied to each language's value" zone — they fold into one All[...] line on
 * serialization. The row validates its own token through the exact mapper the data handler runs
 * later, so an unknown name or a bad argument surfaces on the offending row.
 */
class ExtraPropertyConstraintRowType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('options', HiddenType::class, [
                'required' => false,
            ])
            ->add('per_language', HiddenType::class, [
                'required' => false,
            ]);
    }

    /**
     * Validates the row's DSL token with the exact mapper the data handler runs later, so a row
     * accepted here is guaranteed to be accepted downstream. An abandoned row (no name, no
     * options) is skipped; options without a name get a dedicated message instead of silently
     * serializing to nothing.
     */
    public function validateRow(?array $row, ExecutionContextInterface $context): void
    {
        $row = (array) $row;
        $token = ConstraintRowSerializer::token($row);

        if ('' === $token) {
            if ('' !== trim((string) ($row['options'] ?? ''))) {
                $context->buildViolation(
                    $this->trans('The constraint name is required.', 'Admin.Advparameters.Notification')
                )->atPath('[name]')->addViolation();
            }

            return;
        }

        try {
            ExtraPropertyConstraintMapper::fromNames($token);
        } catch (ExtraPropertyException $e) {
            // The mapper's "Line N: " prefix is meaningless for a single row.
            $context->buildViolation($e->getBareMessage())->addViolation();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            // Keep row-level violations ON the row (compound forms bubble to the root by default).
            'error_bubbling' => false,
            'constraints' => [new Callback($this->validateRow(...))],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'extra_property_constraint_row';
    }
}
