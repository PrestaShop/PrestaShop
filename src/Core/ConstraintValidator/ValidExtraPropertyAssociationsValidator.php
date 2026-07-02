<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\ValidExtraPropertyAssociations;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\AssociationEntryParser;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyDefinitionException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates each line of an association textarea with the exact same parser used by the
 * ExtraPropertyDefinition constructor (AssociationEntryParser), so a value accepted here is
 * guaranteed to be accepted by the value object later.
 *
 * Per line: syntax errors are reported with their line number; duplicate formId/gridId across
 * lines are reported too (mirroring the VO's uniqueness rule). Blank lines are ignored but still
 * counted, so reported line numbers match what the user sees in the textarea.
 */
final class ValidExtraPropertyAssociationsValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidExtraPropertyAssociations) {
            throw new UnexpectedTypeException($constraint, ValidExtraPropertyAssociations::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if ('' === trim($value)) {
            return;
        }

        $seenIds = [];
        foreach (explode("\n", $value) as $index => $line) {
            $lineNumber = $index + 1;
            $entry = trim($line);
            if ('' === $entry) {
                continue;
            }

            try {
                $parsed = match ($constraint->type) {
                    ValidExtraPropertyAssociations::TYPE_FORM => AssociationEntryParser::assertValidFormEntry($entry),
                    ValidExtraPropertyAssociations::TYPE_GRID => AssociationEntryParser::assertValidGridEntry($entry),
                    ValidExtraPropertyAssociations::TYPE_API => AssociationEntryParser::assertValidApiEntry($entry),
                    default => throw new ConstraintDefinitionException(sprintf(
                        'Unknown association type "%s" (allowed: %s).',
                        $constraint->type,
                        implode(', ', [ValidExtraPropertyAssociations::TYPE_FORM, ValidExtraPropertyAssociations::TYPE_GRID, ValidExtraPropertyAssociations::TYPE_API])
                    )),
                };
            } catch (InvalidExtraPropertyDefinitionException $e) {
                // Strip the VO-oriented prefix: in a form context "ExtraPropertyDefinition: " is noise.
                $this->addLineViolation($constraint, $lineNumber, preg_replace('/^ExtraPropertyDefinition: /', '', $e->getMessage()));

                continue;
            }

            $id = match ($constraint->type) {
                ValidExtraPropertyAssociations::TYPE_FORM => $parsed['formId'],
                ValidExtraPropertyAssociations::TYPE_GRID => $parsed['gridId'],
                default => null,
            };
            if (null === $id) {
                continue;
            }

            if (isset($seenIds[$id])) {
                $this->addLineViolation($constraint, $lineNumber, sprintf(
                    ValidExtraPropertyAssociations::TYPE_FORM === $constraint->type
                        ? 'duplicate formId "%s" — each form may only be referenced once.'
                        : 'duplicate gridId "%s" — each grid may only be referenced once.',
                    $id
                ));

                continue;
            }
            $seenIds[$id] = true;
        }
    }

    private function addLineViolation(ValidExtraPropertyAssociations $constraint, int $lineNumber, string $error): void
    {
        $this->context->buildViolation($constraint->message)
            ->setTranslationDomain('Admin.Notifications.Error')
            ->setParameter('%line%', (string) $lineNumber)
            ->setParameter('%error%', $error)
            ->addViolation();
    }
}
