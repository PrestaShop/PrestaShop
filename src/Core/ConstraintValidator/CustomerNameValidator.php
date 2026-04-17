<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CustomerName;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class CustomerNameValidator is responsible for doing the actual validation under the CustomerName constraint.
 */
class CustomerNameValidator extends ConstraintValidator
{
    public const PATTERN_NAME = '/^(?!\s*$)(?:[^0-9!<>,;?=+()\/\\\\@#"°*`{}_^$%:¤\[\]|\.。]|[。\.](?:\s|$))*$/u';
    public const PATTERN_DOT_SPACED = '/[\.。](\s{1}[^\ ]|$)/';

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
        return (bool) preg_match(static::PATTERN_NAME, $name);
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

        return (bool) preg_match(static::PATTERN_DOT_SPACED, $name);
    }
}
