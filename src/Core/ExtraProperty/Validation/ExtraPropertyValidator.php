<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Validation;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Validates extra property values against the Symfony constraints declared on their definitions.
 *
 * Centralizes validation so that ObjectModel, BO form handlers and API integrations all use the same rules.
 * Value validation delegates to the Symfony Validator ($validator->validate($value, $constraints)). For the array
 * shape forms/API always use (LANG = [id_lang|locale => value], SHOP = [id_shop => value]) the value is validated
 * AS-IS: whole-array constraints (e.g. DefaultLanguage) see the array, per-language rules use Symfony's Assert\All.
 * The exception is an ObjectModel loaded WITH a langId, which exposes a LANG value as a single scalar — handled in
 * validateValue(). The batch validate() re-bases each definition's violation paths under "<module>.<property>" so
 * the result is unambiguous. Structural checks (isTableOrIdentifier, isModuleName) use pure regex.
 *
 * Validation is opt-in: a definition with no constraints yields no violations (the storage column type is then the
 * only guard, like any optional field). Requiredness is a constraint too — a module passes Assert\NotBlank when it
 * wants a value to be mandatory.
 *
 * The Symfony validator is a required dependency, available in every container that runs this service: the three
 * Symfony kernels, and the front-office legacy container where it is hand-wired by ValidatorBuilderExtension.
 */
class ExtraPropertyValidator implements ExtraPropertyValidatorInterface
{
    public function __construct(
        protected readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * Checks if a value is a valid SQL table/identifier token:
     * 1–64 characters (MySQL identifier limit), [a-zA-Z0-9_-] only.
     *
     * Static (not part of the interface): called by the ExtraPropertyDefinition
     * constructor, which cannot receive injected services.
     */
    public static function isTableOrIdentifier(string $value): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9_-]{1,64}$/', $value);
    }

    /**
     * Checks if a value is a valid module technical name.
     *
     * Static (not part of the interface): called by the ExtraPropertyDefinition
     * constructor, which cannot receive injected services.
     */
    public static function isModuleName(string $value): bool
    {
        return (bool) preg_match('/^[a-zA-Z0-9_-]+$/', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function validateValue(ExtraPropertyDefinition $definition, mixed $value): ConstraintViolationListInterface
    {
        $constraints = $definition->getConstraints() ?? [];
        if ([] === $constraints) {
            return new ConstraintViolationList();
        }

        // Edge case: an ObjectModel loaded WITH a langId exposes a LANG value as a single scalar (one language),
        // not the [id_lang => value] array forms/API always pass. Validate that scalar against the PER-LANGUAGE
        // rules only — unwrap Assert\All (its nested constraints are the per-language rules) and skip whole-array
        // constraints (e.g. DefaultLanguage, which is meaningless for a single language and whose validator expects
        // an array). For the array shape (no langId / form / API), fall through and validate as-is.
        if (ExtraPropertyScope::LANG === $definition->getScope() && !is_array($value)) {
            $perLanguage = [];
            foreach ($constraints as $constraint) {
                if ($constraint instanceof All) {
                    $perLanguage = array_merge($perLanguage, array_values($constraint->constraints));
                }
            }

            return $this->validator->validate($value, $perLanguage);
        }

        // Value validated as-is: for the LANG/SHOP array shape, whole-array constraints (e.g. DefaultLanguage) see
        // the array while Assert\All applies per-element rules (tagging each violation with a "[<key>]" path); for
        // scalars (COMMON, and SHOP) the bare constraints apply directly.
        return $this->validator->validate($value, $constraints);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $valuesByModule, ExtraPropertyDefinitionCollection $definitions): ConstraintViolationListInterface
    {
        $violations = new ConstraintViolationList();
        foreach ($definitions as $definition) {
            $moduleKey = $definition->getNormalizedModuleKey();
            $propertyName = $definition->getPropertyName();
            if (!isset($valuesByModule[$moduleKey])
                || !is_array($valuesByModule[$moduleKey])
                || !array_key_exists($propertyName, $valuesByModule[$moduleKey])
            ) {
                continue;
            }

            $violations->addAll($this->rebase(
                $this->validateValue($definition, $valuesByModule[$moduleKey][$propertyName]),
                $moduleKey . '.' . $propertyName
            ));
        }

        return $violations;
    }

    /**
     * Re-bases every violation's property path under $prefix, preserving message, template, parameters, root,
     * invalid value, plural and code. Symfony violation paths are immutable, so each violation is reconstructed.
     */
    protected function rebase(ConstraintViolationListInterface $violations, string $prefix): ConstraintViolationListInterface
    {
        $rebased = new ConstraintViolationList();
        foreach ($violations as $violation) {
            $path = $violation->getPropertyPath();
            if ('' === $path) {
                $fullPath = $prefix;
            } elseif (str_starts_with($path, '[')) {
                // Array-key sub-path ("[fr-FR]") attaches directly, no separator.
                $fullPath = $prefix . $path;
            } else {
                $fullPath = $prefix . '.' . $path;
            }

            $rebased->add(new ConstraintViolation(
                $violation->getMessage(),
                $violation->getMessageTemplate(),
                $violation->getParameters(),
                $violation->getRoot(),
                $fullPath,
                $violation->getInvalidValue(),
                $violation->getPlural(),
                $violation->getCode(),
            ));
        }

        return $rebased;
    }
}
