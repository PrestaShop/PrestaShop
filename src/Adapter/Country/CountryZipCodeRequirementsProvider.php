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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Country;

use Country;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Country\CountryZipCodeRequirements;
use PrestaShop\PrestaShop\Core\Country\CountryZipCodeRequirementsProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Country\ZipCodePatternResolver;
use PrestaShopException;

final class CountryZipCodeRequirementsProvider implements CountryZipCodeRequirementsProviderInterface
{
    /**
     * @var int
     */
    private $langId;

    /**
     * @var ZipCodePatternResolver
     */
    private $patternResolver;

    /**
     * @param LegacyContext $context
     * @param ZipCodePatternResolver $patternResolver
     */
    public function __construct(LegacyContext $context, ZipCodePatternResolver $patternResolver)
    {
        $this->langId = (int) $context->getLanguage()->id;
        $this->patternResolver = $patternResolver;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CountryNotFoundException
     */
    public function getCountryZipCodeRequirements(CountryId $countryId): CountryZipCodeRequirements
    {
        $countryIdValue = $countryId->getValue();

        try {
            $country = new Country($countryIdValue);
        } catch (PrestaShopException $e) {
            throw new CountryNotFoundException(sprintf('Country with id "%s" was not found.', $countryIdValue));
        }

        if ($country->id !== $countryIdValue) {
            throw new CountryNotFoundException(sprintf('Country with id "%s" was not found.', $countryIdValue));
        }

        $requirements = new CountryZipCodeRequirements($country->need_zip_code);

        if (isset($country->name[$this->langId])) {
            $requirements->setCountryName($country->name[$this->langId]);
        }

        if ($country->need_zip_code && !empty($country->zip_code_format)) {
            $pattern = $this->patternResolver->getRegexPattern($country->zip_code_format, $country->iso_code);
            $humanReadablePattern = $this->patternResolver->getHumanReadablePattern(
                $country->zip_code_format,
                $country->iso_code
            );

            $requirements->setPatterns($pattern, $humanReadablePattern);
        }

        return $requirements;
    }
}
