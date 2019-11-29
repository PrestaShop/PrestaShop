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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\ExistingCustomerEmail;
use PrestaShop\PrestaShop\Core\Customer\CustomerDataSourceInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validator for checking if customer with given email exists in current shop context
 */
final class ExistingCustomerEmailValidator extends ConstraintValidator
{
    /**
     * @var CustomerDataSourceInterface
     */
    private $customerDataSource;

    /**
     * @param CustomerDataSourceInterface $customerDataSource
     */
    public function __construct(CustomerDataSourceInterface $customerDataSource)
    {
        $this->customerDataSource = $customerDataSource;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ExistingCustomerEmail) {
            throw new UnexpectedTypeException($constraint, ExistingCustomerEmail::class);
        }

        if (!$this->customerDataSource->hasCustomerByEmail($value)) {
            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain('Admin.Orderscustomers.Notification')
                ->addViolation()
            ;
        }
    }
}
