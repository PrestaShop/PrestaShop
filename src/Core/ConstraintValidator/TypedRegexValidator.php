<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Address\Configuration\AddressConstraint;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\AlphaIsoCode;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;
use PrestaShop\PrestaShop\Core\String\CharacterCleaner;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates specific regex pattern for provided type
 */
class TypedRegexValidator extends ConstraintValidator
{
    /**
     * @var CharacterCleaner
     */
    private $characterCleaner;

    /**
     * @param CharacterCleaner $characterCleaner
     */
    public function __construct(CharacterCleaner $characterCleaner)
    {
        $this->characterCleaner = $characterCleaner;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof TypedRegex) {
            throw new UnexpectedTypeException($constraint, TypedRegex::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $pattern = $this->getPattern($constraint->type);
        $value = $this->sanitize($value, $constraint->type);

        if (!$this->match($pattern, $constraint->type, $value)) {
            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain('Admin.Notifications.Error')
                ->setParameter('%s', $this->formatValue($value))
                ->addViolation()
            ;
        }
    }

    /**
     * Returns regex pattern that depends on type
     *
     * @param string $type
     *
     * @return string
     */
    private function getPattern($type)
    {
        $typePatterns = [
            TypedRegex::TYPE_NAME => $this->characterCleaner->cleanNonUnicodeSupport('/^[^0-9!<>,;?=+()@#"°{}_$%:¤|]*$/u'),
            TypedRegex::TYPE_CATALOG_NAME => $this->characterCleaner->cleanNonUnicodeSupport('/^[^<>;=#{}]*$/u'),
            TypedRegex::TYPE_GENERIC_NAME => $this->characterCleaner->cleanNonUnicodeSupport('/^[^<>={}]*$/u'),
            TypedRegex::TYPE_CITY_NAME => $this->characterCleaner->cleanNonUnicodeSupport('/^[^!<>;?=+@#"°{}_$%]*$/u'),
            TypedRegex::TYPE_ADDRESS => $this->characterCleaner->cleanNonUnicodeSupport('/^[^!<>?=+@{}_$%]*$/u'),
            TypedRegex::TYPE_POST_CODE => '/^[a-zA-Z 0-9-]+$/',
            TypedRegex::TYPE_PHONE_NUMBER => '/^[+0-9. ()\/-]*$/',
            TypedRegex::TYPE_MESSAGE => '/[<>{}]/i',
            TypedRegex::TYPE_LANGUAGE_ISO_CODE => IsoCode::PATTERN,
            TypedRegex::TYPE_LANGUAGE_CODE => '/^[a-zA-Z]{2}(-[a-zA-Z]{2})?$/',
            TypedRegex::TYPE_CURRENCY_ISO_CODE => AlphaIsoCode::PATTERN,
            TypedRegex::TYPE_FILE_NAME => '/^[a-zA-Z0-9_.-]+$/',
            TypedRegex::TYPE_DNI_LITE => AddressConstraint::DNI_LITE_PATTERN,
        ];

        if (isset($typePatterns[$type])) {
            return $typePatterns[$type];
        }

        throw new InvalidArgumentException(sprintf('Type "%s" is not defined. Defined types are: %s', $type, implode(',', array_keys($typePatterns))));
    }

    /**
     * Responsible for sanitizing the string depending on type. (eg. applying  stripslashes())
     *
     * @param string $value
     * @param string $type
     *
     * @return string
     */
    private function sanitize($value, $type)
    {
        if ($type === 'name') {
            $value = stripslashes($value);
        }

        return $value;
    }

    /**
     * Responsible for applying preg_match depending on type.
     * preg_match returns 1 if the pattern
     * matches given subject, 0 if it does not, or FALSE
     * if an error occurred.
     *
     * @param $pattern
     * @param $type
     * @param $value
     *
     * @return false|int
     */
    private function match($pattern, $type, $value)
    {
        $typesToInverseMatching = ['message'];

        if (in_array($type, $typesToInverseMatching, true)) {
            return !preg_match($pattern, $value);
        }

        return preg_match($pattern, $value);
    }
}
