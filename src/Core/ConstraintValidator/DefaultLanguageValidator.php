<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class DefaultLanguageValidator is responsilbe for doing the actual validation under DefaultLanguage constraint.
 */
class DefaultLanguageValidator extends ConstraintValidator
{
    public function __construct(
        private readonly LanguageContext $defaultLanguageContext,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof DefaultLanguage) {
            throw new UnexpectedTypeException($constraint, DefaultLanguage::class);
        }

        if ($constraint->allowNull) {
            if (null === $value) {
                return;
            }
        }

        // If allowNull is false and value is null, we should build a violation instead of throwing an exception
        if (null === $value) {
            $fieldName = $constraint->fieldName;
            if (empty($fieldName) && $this->context->getObject() instanceof Form) {
                $fieldName = $this->context->getObject()->getName();
            }

            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain('Admin.Notifications.Error')
                ->setParameter('%field_name%', $fieldName)
                ->addViolation()
            ;

            return;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        if ($constraint->allowNull && !isset($value[$this->defaultLanguageContext->getId()]) && !isset($value[$this->defaultLanguageContext->getLocale()])) {
            // Check that the array actually contains a value for the default language (regardless of its empty value or not), if no index
            // matching the default language is set it means there is no planned modification
            return;
        }

        // Check if value for default language is present, we use language ID for back-office form's usage, and we use the locale
        // for Admin API usages since the input data is indexed by locale thanks to the LocalizedValue attribute
        if (empty($value[$this->defaultLanguageContext->getId()]) && empty($value[$this->defaultLanguageContext->getLocale()])) {
            $fieldName = $constraint->fieldName;
            if (empty($fieldName) && $this->context->getObject() instanceof Form) {
                $fieldName = $this->context->getObject()->getName();
            }

            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain('Admin.Notifications.Error')
                ->setParameter('%field_name%', $fieldName)
                ->addViolation()
            ;
        }
    }
}
