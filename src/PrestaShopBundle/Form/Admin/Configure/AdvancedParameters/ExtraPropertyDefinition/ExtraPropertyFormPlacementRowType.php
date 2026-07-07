<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\AssociationEntryParser;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyDefinitionException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\AssociationRowSerializer;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * One row of the "Forms" placement subsection — a MAPPED collection entry carrying one
 * associated_forms entry ("formId[:path[:before|after]]") split into its explicit parts. The data
 * handler serializes the rows back through AssociationRowSerializer.
 *
 * form_id is deliberately a free TextType (never a ChoiceType): pointing at a form the catalog
 * does not know is a supported manual override and must not block submission. The row validates
 * its own GRAMMAR on submit (the same assertValid* check the ExtraPropertyDefinition constructor
 * runs) so a malformed entry surfaces on the offending row; cross-row rules (duplicate ids) live
 * on the collection (see ExtraPropertyDefinitionAdvancedType).
 */
class ExtraPropertyFormPlacementRowType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('form_id', TextType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('path', TextType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('mode', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'choices' => [
                    $this->trans('Automatic (default placement)', 'Admin.Advparameters.Feature') => '',
                    $this->trans('Before', 'Admin.Advparameters.Feature') => 'before',
                    $this->trans('After', 'Admin.Advparameters.Feature') => 'after',
                ],
                'invalid_message' => $this->trans('Invalid placement position.', 'Admin.Advparameters.Notification'),
            ]);
    }

    /**
     * Validates the row's serialized entry with the exact parser the value object runs later, so a
     * row accepted here is guaranteed to be accepted downstream. An all-empty row is skipped (the
     * collection's delete_empty already dropped it); a row with content but no id gets a dedicated
     * message instead of silently serializing to nothing.
     */
    public function validateRow(?array $row, ExecutionContextInterface $context): void
    {
        $entries = AssociationRowSerializer::formEntries([(array) $row]);

        if ([] === $entries) {
            if ([] !== array_filter((array) $row, static fn (?string $value): bool => '' !== trim((string) $value))) {
                $context->buildViolation(
                    $this->trans('The form identifier is required.', 'Admin.Advparameters.Notification')
                )->atPath('[form_id]')->addViolation();
            }

            return;
        }

        try {
            AssociationEntryParser::assertValidFormEntry($entries[0]);
        } catch (InvalidExtraPropertyDefinitionException $e) {
            // The VO-oriented "ExtraPropertyDefinition: " prefix is noise on the row itself.
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
        return 'extra_property_form_placement_row';
    }
}
