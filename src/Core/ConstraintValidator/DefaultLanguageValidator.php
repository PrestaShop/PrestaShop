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
