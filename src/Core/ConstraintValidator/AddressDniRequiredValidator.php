<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressDniRequired;
use PrestaShop\PrestaShop\Core\Country\CountryRequiredFieldsProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Validates address DNI according to the country value and fields requirement
 */
class AddressDniRequiredValidator extends ConstraintValidator
{
    /**
     * @var CountryRequiredFieldsProviderInterface
     */
    private $countryRequiredFieldsProvider;

    /**
     * @param CountryRequiredFieldsProviderInterface $countryRequiredFieldsProvider
     */
    public function __construct(CountryRequiredFieldsProviderInterface $countryRequiredFieldsProvider)
    {
        $this->countryRequiredFieldsProvider = $countryRequiredFieldsProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!($constraint instanceof AddressDniRequired)) {
            return;
        }
        $countryId = new CountryId((int) $constraint->id_country);

        if ($this->countryRequiredFieldsProvider->isDniRequired($countryId) || $constraint->required) {
            $constraints = [new NotBlank([
                'message' => $constraint->message,
            ])];

            /** @var ConstraintViolationInterface[] $violations */
            $violations = $this->context->getValidator()->validate($value, $constraints);
            foreach ($violations as $violation) {
                $this->context->buildViolation($violation->getMessage())
                    ->setTranslationDomain('Admin.Notifications.Error')
                    ->addViolation();
            }
        }
    }
}
