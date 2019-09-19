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

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountryZipCodeRequirements;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\CountryZipCodeRequirements;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validate zip codes in range
 */
class ZipCodeRangeValidator extends ConstraintValidator
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var FormChoiceProviderInterface
     */
    private $countryChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param CommandBusInterface $queryBus
     * @param FormChoiceProviderInterface $countryChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        CommandBusInterface $queryBus,
        FormChoiceProviderInterface $countryChoiceProvider
    ) {
        $this->translator = $translator;
        $this->queryBus = $queryBus;
        $this->countryChoiceProvider = $countryChoiceProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CountryConstraintException
     */
    public function validate($value, Constraint $constraint)
    {
        $countryId = (int) $value['country'];
        $selectedCountries = [$countryId];
        $zipCode = $value['zipCode'];

        if (null === $zipCode) {
            return;
        }

        if ($countryId === 0) {
            $countries = $this->countryChoiceProvider->getChoices();
            $selectedCountries = array_map('intval', $countries);
        }

        foreach ($selectedCountries as $country) {
            /** @var CountryZipCodeRequirements $requirements */
            $requirements = $this->queryBus->handle(new GetCountryZipCodeRequirements($country));

            foreach (explode('-', $zipCode) as $code) {
                $isInvalid = (bool) $code &&
                    null !== $requirements->getPattern() &&
                    !(bool) preg_match($requirements->getPattern(), trim($code));

                if ($isInvalid) {
                    $message = $this->translator->trans(
                        'The Zip/postal code is invalid. It must be typed as follows: %format% for %country%.',
                        [
                            '%format%' => $requirements->getHumanReadablePattern(),
                            '%country%' => $requirements->getCountryName() !== null ?
                                $requirements->getCountryName() :
                                $this->translator->trans('Country selection', [], 'Admin.Catalog.Feature'),
                        ],
                        'Admin.Notifications.Error'
                    );

                    $this->context->buildViolation($message)
                        ->addViolation()
                    ;

                    continue 2;
                }
            }
        }
    }
}
