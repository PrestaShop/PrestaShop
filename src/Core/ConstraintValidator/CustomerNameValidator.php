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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CustomerName;
use PrestaShop\PrestaShop\Core\String\CharacterCleaner;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class CustomerNameValidator is responsilbe for doing the actual validation under CustomerName constraint.
 */
class CustomerNameValidator extends ConstraintValidator
{
    public const PATTERN_NAME = '/^(?!\s*$)(?:[^0-9!<>,;?=+()\/\\\\@#"°*`{}_^$%:¤\[\]|\.。]|[。\.](?:\s|$))*$/u';
    public const PATTERN_DOT_SPACED = '/[\.。](\s{1}[^\ ]|$)/';

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
        if (!$constraint instanceof CustomerName) {
            throw new UnexpectedTypeException($constraint, CustomerName::class);
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$this->isNameValid($value) || !$this->isPointSpacedValid($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }

    /**
     * Validates url rewrite according a specific pattern.
     *
     * @param string $name
     *
     * @return bool
     */
    private function isNameValid($name)
    {
        $pattern = $this->characterCleaner->cleanNonUnicodeSupport(self::PATTERN_NAME);

        return (bool) preg_match($pattern, $name);
    }

    /**
     * Check if there is not more one space after point
     *
     * @param string $name
     *
     * @return bool
     */
    private function isPointSpacedValid($name)
    {
        if (mb_strpos($name, '.') === false && mb_strpos($name, '。') === false) {
            return true;
        }
        $pattern = $this->characterCleaner->cleanNonUnicodeSupport(self::PATTERN_DOT_SPACED);

        return (bool) preg_match($pattern, $name);
    }
}
