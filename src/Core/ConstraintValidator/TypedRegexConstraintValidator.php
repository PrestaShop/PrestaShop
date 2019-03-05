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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegexConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates specific regex pattern for provided type
 */
class TypedRegexConstraintValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof TypedRegexConstraint) {
            throw new UnexpectedTypeException($constraint, TypedRegexConstraint::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!preg_match($this->getPattern($constraint->type), $value)) {
            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain('Admin.Notifications.Error')
                ->setParameter('%s', $this->formatValue($value))
                ->addViolation()
            ;
        }
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function getPattern($type)
    {
        $typePatterns = [
            'catalog_name' => $this->cleanNonUnicodeSupport('/^[^<>;=#{}]*$/u'),
            'generic_name' => $this->cleanNonUnicodeSupport('/^[^<>={}]*$/u'),
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
}
