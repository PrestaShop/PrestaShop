<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Factory;

use PrestaShop\PrestaShop\Core\ConstraintValidator\CustomerNameValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;

class CustomerNameValidatorFactory implements ConstraintValidatorFactoryInterface
{
    /**
     * @param Constraint $constraint
     *
     * @return ConstraintValidatorInterface
     */
    public function getInstance(Constraint $constraint): ConstraintValidatorInterface
    {
        return new CustomerNameValidator();
    }
}
