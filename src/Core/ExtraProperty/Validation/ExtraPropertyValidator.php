<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Validation;

use DateTimeImmutable;
use DateTimeInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
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
     * The single rule-set for "can this value be stored under the declared type" — shared
     * by the registry (default values, ExtraPropertyRegistry::isDefaultValueCompatible())
     * and by validateValue() (every regular write), so what is refused as a default is
     * refused as a value and vice versa. Values may arrive as native scalars (module code,
     * Admin API JSON) or as strings (BO form fields) — both spellings of a valid value are
     * accepted, plus the runtime-only shapes (DateTimeInterface for DATE, an already
     * decoded structure for JSON). Null and '' always pass: "no value" is the storage
     * column's concern (nullability) or a declared constraint's (requiredness).
     *
     * Static (not part of the interface): also called by the registry, which validates
     * defaults with its own dedicated exception.
     */
    public static function isValueCompatible(ExtraPropertyType $type, mixed $value, ?array $enumValues = null): bool
    {
        if (null === $value || '' === $value) {
            return true;
        }

        return match ($type) {
            ExtraPropertyType::INT => is_int($value)
                || (is_string($value) && 1 === preg_match('/^-?\d+$/', $value)),
            ExtraPropertyType::FLOAT => is_int($value) || is_float($value)
                || (is_string($value) && is_numeric($value)),
            ExtraPropertyType::BOOL => is_bool($value) || in_array($value, [0, 1, '0', '1'], true),
            // Only literal datetimes: relative wordings ('tomorrow') are never interpreted,
            // neither as defaults nor as stored values.
            ExtraPropertyType::DATE => $value instanceof DateTimeInterface
                || (is_string($value) && self::isLiteralDateTime($value)),
            ExtraPropertyType::CHOICE => null === $enumValues
                || (is_scalar($value) && in_array((string) $value, $enumValues, true)),
            ExtraPropertyType::JSON => is_array($value)
                || (is_string($value) && (null !== json_decode($value) || 'null' === trim($value))),
            default => true,
        };
    }

    /**
     * A literal 'Y-m-d' or 'Y-m-d H:i:s' datetime. The round-trip format comparison also
     * rejects impossible dates that createFromFormat() would silently roll over
     * ('2026-02-31' parses as March 3rd).
     */
    protected static function isLiteralDateTime(string $value): bool
    {
        foreach (['Y-m-d H:i:s', 'Y-m-d'] as $format) {
            $date = DateTimeImmutable::createFromFormat($format, $value);
            if (false !== $date && $date->format($format) === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function validateValue(ExtraPropertyDefinition $definition, mixed $value): ConstraintViolationListInterface
    {
        $declaredViolations = $this->validateDeclaredConstraints($definition, $value);
        if (0 !== $declaredViolations->count()) {
            return $declaredViolations;
        }

        // Implicit minimal type validation, applied even when no constraint is declared:
        // the same isValueCompatible() rules the registry applies to default values, so a
        // value that could not be a default is not storable either (no silent coercion —
        // 'abc' on an INT used to be stored as 0, 'tomorrow' on a DATE was interpreted).
        // Runs AFTER the declared constraints so their messages keep priority (a module's
        // Assert\Type('bool') reports its own wording, not the generic type message).
        return $this->validateTypeCompatibility($definition, $value);
    }

    /**
     * Validates the value against the definition's DECLARED Symfony constraints only —
     * the historical opt-in behaviour; validateValue() adds the implicit type safety net.
     */
    protected function validateDeclaredConstraints(ExtraPropertyDefinition $definition, mixed $value): ConstraintViolationListInterface
    {
        $constraints = $definition->getConstraints() ?? [];
        if ([] === $constraints) {
            return new ConstraintViolationList();
        }

        // JSON values may legitimately arrive as decoded structures (API payloads, module
        // code writing arrays): constraints like Assert\Json expect the ENCODED string —
        // the storage form — so normalize that one case before validating. Other types are
        // validated as submitted (a bool stays a bool for Assert\Type('bool'); the writer
        // applies its own storage coercion after validation).
        if (ExtraPropertyType::JSON === $definition->getType() && !is_string($value) && null !== $value) {
            $value = json_encode($value);
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
     * Applies isValueCompatible() to the submitted value. The LANG/SHOP array shape
     * ([id_lang|locale => value] / [id_shop => value]) is checked leaf by leaf, each
     * violation tagged with its "[<key>]" sub-path — except for JSON, whose array shape IS
     * the (decoded) value. A scalar (COMMON/SHOP scalar, or the single-language value an
     * ObjectModel loaded with a langId exposes) is checked directly.
     */
    protected function validateTypeCompatibility(ExtraPropertyDefinition $definition, mixed $value): ConstraintViolationList
    {
        $violations = new ConstraintViolationList();
        $type = $definition->getType();

        if (is_array($value) && ExtraPropertyType::JSON !== $type) {
            foreach ($value as $key => $leaf) {
                if (!self::isValueCompatible($type, $leaf, $definition->getEnumValues())) {
                    $violations->add($this->buildTypeViolation($type, $leaf, sprintf('[%s]', $key)));
                }
            }

            return $violations;
        }

        if (!self::isValueCompatible($type, $value, $definition->getEnumValues())) {
            $violations->add($this->buildTypeViolation($type, $value, ''));
        }

        return $violations;
    }

    protected function buildTypeViolation(ExtraPropertyType $type, mixed $invalidValue, string $path): ConstraintViolation
    {
        $template = 'The value is not compatible with the declared "{{ type }}" field type.';

        return new ConstraintViolation(
            str_replace('{{ type }}', $type->value, $template),
            $template,
            ['{{ type }}' => $type->value],
            $invalidValue,
            $path,
            $invalidValue
        );
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
