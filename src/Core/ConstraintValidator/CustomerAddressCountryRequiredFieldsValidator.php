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

use PrestaShop\PrestaShop\Core\Country\CountryRequiredFieldsProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates customer address state choice by selected country value
 */
final class CustomerAddressCountryRequiredFieldsValidator extends ConstraintValidator
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
        $countryId = (int) $value['id_country'];
        $stateId = isset($value['id_state']) ? $value['id_state'] : null;
        $dni = isset($value['dni']) ? $value['dni'] : null;

        $countryIdVo = new CountryId($countryId);

        if ($this->countryRequiredFieldsProvider->isStatesRequired($countryIdVo) && null === $stateId) {
            $this->context->buildViolation($constraint->stateRequiredMessage)
                ->atPath('[id_state]')
                ->setTranslationDomain('Admin.Orderscustomers.Notification')
                ->addViolation()
            ;
        }

        if (!$this->countryRequiredFieldsProvider->isStatesRequired($countryIdVo) && null !== $stateId) {
            $this->context->buildViolation($constraint->countryStateMessage)
                ->atPath('[id_state]')
                ->setTranslationDomain('Admin.Orderscustomers.Notification')
                ->addViolation()
            ;
        }

        if ($this->countryRequiredFieldsProvider->isDniRequired($countryIdVo) && null !== $dni) {
            $this->context->buildViolation($constraint->message)
                ->atPath('[dni]')
                ->setTranslationDomain('Admin.Notifications.Error')
                ->addViolation()
            ;
        }
    }
}
