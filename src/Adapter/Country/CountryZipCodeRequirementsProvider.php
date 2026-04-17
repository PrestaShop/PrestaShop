<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        } catch (PrestaShopException) {
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
