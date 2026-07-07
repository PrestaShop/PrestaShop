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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * One row of the "Admin API" placement subsection — a MAPPED collection entry carrying one
 * associated_apis entry ("uriPath[:METHOD[,METHOD...]]"). Same contract as
 * ExtraPropertyFormPlacementRowType: the URI stays free text so endpoints outside the catalog
 * never block saving, and the row validates its own grammar on submit.
 *
 * methods holds the uppercase CSV the method chips write back ("GET,PATCH"); empty means the
 * entry matches every method (a bare entry, no ":METHODS" suffix).
 */
class ExtraPropertyApiPlacementRowType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('uri', TextType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('methods', TextType::class, [
                'label' => false,
                'required' => false,
            ]);
    }

    /**
     * Validates the row's serialized entry with the exact parser the value object runs later —
     * see ExtraPropertyFormPlacementRowType::validateRow() for the shape of the check.
     */
    public function validateRow(?array $row, ExecutionContextInterface $context): void
    {
        $entries = AssociationRowSerializer::apiEntries([(array) $row]);

        if ([] === $entries) {
            if ([] !== array_filter((array) $row, static fn (?string $value): bool => '' !== trim((string) $value))) {
                $context->buildViolation(
                    $this->trans('The endpoint path is required.', 'Admin.Advparameters.Notification')
                )->atPath('[uri]')->addViolation();
            }

            return;
        }

        try {
            AssociationEntryParser::assertValidApiEntry($entries[0]);
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
        return 'extra_property_api_placement_row';
    }
}
