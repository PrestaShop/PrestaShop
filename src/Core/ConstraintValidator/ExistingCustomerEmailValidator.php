<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

        if (!$this->customerDataSource->hasCustomerWithEmail($value)) {
            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain('Admin.Orderscustomers.Notification')
                ->addViolation()
            ;
        }
    }
}
