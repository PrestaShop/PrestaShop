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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Validator\Constraints;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Validate;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MultipleEmailsWithSeparatorValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof MultipleEmailsWithSeparator) {
            throw new UnexpectedTypeException($constraint, MultipleEmailsWithSeparator::class);
        }

        $translator = (new LegacyContext())->getContext()->getTranslator();

        if (!Validate::isString($value)) {
            throw new \InvalidArgumentException('Value must be string. Input was: ' . \gettype($value));
        }

        $emailsList = array_map('trim', explode($constraint->separator, $value));

        $invalidEmails = [];

        foreach ($emailsList as $email) {
            if (!Validate::isEmail($email)) {
                $invalidEmails[] = $email;
            }
        }

        if (!empty($invalidEmails)) {
            $message = $constraint->message ?? $translator->trans(
                'Invalid email(s) : %invalid_emails%.',
                ['%invalid_emails%' => implode(',', $invalidEmails)],
                'Admin.Global.Notification'
            );

            $this->context->buildViolation($message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(MultipleEmailsWithSeparator::INVALID_EMAILS_ERROR_CODE)
                ->addViolation()
            ;
        }
    }
}
