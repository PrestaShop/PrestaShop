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
