<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressZipCode;
use PrestaShop\PrestaShop\Core\Country\CountryZipCodeRequirementsProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validator for address zip code value
 */
final class AddressZipCodeValidator extends ConstraintValidator
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var CountryZipCodeRequirementsProviderInterface
     */
    private $requirementsProvider;

    /**
     * @param TranslatorInterface $translator
     * @param CountryZipCodeRequirementsProviderInterface $requirementsProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        CountryZipCodeRequirementsProviderInterface $requirementsProvider
    ) {
        $this->translator = $translator;
        $this->requirementsProvider = $requirementsProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CountryConstraintException
     */
    public function validate($value, Constraint $constraint)
    {
        if (!($constraint instanceof AddressZipCode)) {
            return;
        }
        $countryId = (int) $constraint->id_country;

        $requirements = $this->requirementsProvider->getCountryZipCodeRequirements(new CountryId($countryId));

        if ($requirements->isRequired() || $constraint->required) {
            $constraints = [new NotBlank([
                'message' => $constraint->requiredMessage,
            ])];

            /** @var ConstraintViolationInterface[] $violations */
            $violations = $this->context->getValidator()->validate($value, $constraints);
            foreach ($violations as $violation) {
                $this->context->buildViolation($violation->getMessage())
                    ->setTranslationDomain('Admin.Notifications.Error')
                    ->addViolation();
            }
        }

        if (null !== $requirements->getPattern() && !(bool) preg_match($requirements->getPattern(), $value)) {
            $message = $this->translator->trans('Your Zip/Postal code is incorrect.', [], 'Admin.Notifications.Error') .
                ' ' .
                $this->translator->trans('It must be entered as follows:', [], 'Admin.Notifications.Error') . ' ' .
                $requirements->getHumanReadablePattern()
            ;

            $this->context->buildViolation($message)->addViolation();
        }
    }
}
