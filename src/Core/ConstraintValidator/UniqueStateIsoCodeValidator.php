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

use PrestaShop\PrestaShop\Adapter\State\CountryStateByIsoCodeProvider;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\UniqueStateIsoCode;
use PrestaShopBundle\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validator for checking if state with ISO Code doesn't exist in current shop context
 */
class UniqueStateIsoCodeValidator extends ConstraintValidator
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!($constraint instanceof UniqueStateIsoCode)) {
            return;
        }
        $countrySTateByIsoCodeProvider = new CountryStateByIsoCodeProvider();
        $stateId = $countrySTateByIsoCodeProvider->getStateIdByIsoCode($value);

        if ($stateId && ($constraint->excludeId === null || $stateId !== $constraint->excludeId)) {
            $this->context
                ->buildViolation(
                    $this->translator->trans(
                        'This ISO code already exists. You cannot create two states with the same ISO code.',
                        [],
                        'Admin.International.Notification'
                    )
                )
                ->atPath('[iso_code]')
                ->addViolation();
        }
    }
}
