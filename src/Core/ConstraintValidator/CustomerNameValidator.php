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

use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CustomerName;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class CustomerNameValidator is responsilbe for doing the actual validation under CustomerName constraint.
 */
class CustomerNameValidator extends ConstraintValidator
{
    const PATTERN_NAME = '/^(?:[^0-9!<>,;?=+()\/\\\\@#"°*`{}_^$%:¤\[\]|\.]|[\.](?:\s|$))*$/u';

    /**
     * @var Tools
     */
    private $tools;

    /**
     * @param Tools $tools
     */
    public function __construct(Tools $tools)
    {
        $this->tools = $tools;
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
     * @return false|int
     */
    private function isNameValid($name)
    {
        $pattern = $this->tools->cleanNonUnicodeSupport(self::PATTERN_NAME);

        return preg_match($pattern, $name);
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
        $lastCharPos = strlen($name) - 1;
        if ((strpos($name, '.') === false || strpos($name, '.') === $lastCharPos)
            && (strpos($name, '。') === false || strpos($name, '。') === $lastCharPos)) {
            return true;
        }

        return (strpos($name, '. ') !== false || strpos($name, '。 ') !== false)
            && strpos($name, '.  ') === false && strpos($name, '。  ') === false;
    }
}
