<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\Country\CountryRequiredFieldsProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Validates address state choice by selected country value
 */
class AddressStateRequiredValidator extends ConstraintValidator
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
     *
     * @throws CountryConstraintException
     */
    public function validate($value, Constraint $constraint)
    {
        $countryId = new CountryId((int) $constraint->id_country);

        if ($this->countryRequiredFieldsProvider->isStatesRequired($countryId)) {
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
