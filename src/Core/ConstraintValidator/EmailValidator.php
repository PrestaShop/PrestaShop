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

use Egulias\EmailValidator\EmailValidator as EguliasEmailValidator;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\Email;
use PrestaShop\PrestaShop\Core\Email\SwiftMailerValidation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class EmailValidator is responsible for doing the actual validation under Email constraint.
 */
class EmailValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Email) {
            throw new UnexpectedTypeException($constraint, Email::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!$this->validateWithEgulias($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }

    public function validateWithEgulias(mixed $value): bool
    {
        $eguliasValidator = new EguliasEmailValidator();

        $validations = [
            new RFCValidation(),
            new SwiftMailerValidation(), // special validation to be compatible with Swift Mailer
        ];

        return $eguliasValidator->isValid($value, new MultipleValidationWithAnd($validations));
    }
}
