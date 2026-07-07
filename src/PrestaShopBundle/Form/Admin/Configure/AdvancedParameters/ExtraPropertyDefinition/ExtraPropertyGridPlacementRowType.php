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
 * One row of the "Grids" placement subsection — a MAPPED collection entry carrying one
 * associated_grids entry ("gridId[:columnId[:before|after]]") split into its explicit parts. Same
 * contract as ExtraPropertyFormPlacementRowType: ids stay free text so unknown grids never block
 * saving, the row validates its own grammar on submit, cross-row rules live on the collection.
 *
 * mode keeps only an EXPLICIT ":before"/":after" suffix ("" otherwise) so serializing the row
 * re-emits the original entry — the runtime's "after" default for a bare "gridId:columnId" entry
 * is a display concern, not a stored one.
 */
class ExtraPropertyGridPlacementRowType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('grid_id', TextType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('column_id', TextType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('mode', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'choices' => [
                    $this->trans('Automatic (end of grid)', 'Admin.Advparameters.Feature') => '',
                    $this->trans('Before', 'Admin.Advparameters.Feature') => 'before',
                    $this->trans('After', 'Admin.Advparameters.Feature') => 'after',
                ],
                'invalid_message' => $this->trans('Invalid placement position.', 'Admin.Advparameters.Notification'),
            ]);
    }

    /**
     * Validates the row's serialized entry with the exact parser the value object runs later —
     * see ExtraPropertyFormPlacementRowType::validateRow() for the shape of the check.
     */
    public function validateRow(?array $row, ExecutionContextInterface $context): void
    {
        $entries = AssociationRowSerializer::gridEntries([(array) $row]);

        if ([] === $entries) {
            if ([] !== array_filter((array) $row, static fn (?string $value): bool => '' !== trim((string) $value))) {
                $context->buildViolation(
                    $this->trans('The grid identifier is required.', 'Admin.Advparameters.Notification')
                )->atPath('[grid_id]')->addViolation();
            }

            return;
        }

        try {
            AssociationEntryParser::assertValidGridEntry($entries[0]);
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
        return 'extra_property_grid_placement_row';
    }
}
