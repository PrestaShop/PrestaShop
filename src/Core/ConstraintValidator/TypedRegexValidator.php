<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
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
            'name' => $this->cleanNonUnicodeSupport('/^[^0-9!<>,;?=+()@#"°{}_$%:¤|]*$/u'),
            'catalog_name' => $this->cleanNonUnicodeSupport('/^[^<>;=#{}]*$/u'),
            'generic_name' => $this->cleanNonUnicodeSupport('/^[^<>={}]*$/u'),
            'city_name' => $this->cleanNonUnicodeSupport('/^[^!<>;?=+@#"°{}_$%]*$/u'),
            'address' => $this->cleanNonUnicodeSupport('/^[^!<>?=+@{}_$%]*$/u'),
            'post_code' => '/^[a-zA-Z 0-9-]+$/',
            'phone_number' => '/^[+0-9. ()\/-]*$/',
            'message' => '/[<>{}]/i',
            'language_iso_code' => IsoCode::PATTERN,
            'language_code' => '/^[a-zA-Z]{2}(-[a-zA-Z]{2})?$/',
            'dni_lite' => '/^[0-9A-Za-z-.]{1,16}$/U',
        ];

        if (isset($typePatterns[$type])) {
            return $typePatterns[$type];
        }

        throw new InvalidArgumentException(sprintf(
            'Type "%s" is not defined. Defined types are: %s',
            $type,
            implode(',', array_keys($typePatterns))
        ));
    }

    /**
     * Delete unicode class from regular expression patterns.
     * Cleaning non unicode is optional. Refer to legacy Validate to see if it's needed.
     *
     * @param string $pattern
     *
     * @return string pattern
     */
    private function cleanNonUnicodeSupport($pattern)
    {
        if (!defined('PREG_BAD_UTF8_OFFSET')) {
            return $pattern;
        }

        return preg_replace('/\\\[px]\{[a-z]{1,2}\}|(\/[a-z]*)u([a-z]*)$/i', '$1$2', $pattern);
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
