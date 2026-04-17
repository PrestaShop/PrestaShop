<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

use Country as LegacyCountry;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepository;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

class CountryContextBuilder implements LegacyContextBuilderInterface
{
    use LegacyObjectCheckerTrait;

    private ?int $countryId = null;

    private ?LegacyCountry $legacyCountry = null;

    public function __construct(
        private readonly CountryRepository $countryRepository,
        private readonly ContextStateManager $contextStateManager,
        private readonly LanguageContext $languageContext,
    ) {
    }

    public function build(): CountryContext
    {
        $this->assertArguments();
        $legacyCountry = $this->getLegacyCountry();

        return new CountryContext(
            id: (int) $legacyCountry->id,
            zoneId: (int) $legacyCountry->id_zone,
            currencyId: (int) $legacyCountry->id_currency,
            isoCode: $legacyCountry->iso_code,
            callPrefix: (int) $legacyCountry->call_prefix,
            name: $legacyCountry->name[$this->languageContext->getId()] ?? reset($legacyCountry->name),
            containsStates: (bool) $legacyCountry->contains_states,
            identificationNumberNeeded: (bool) $legacyCountry->need_identification_number,
            zipCodeNeeded: (bool) $legacyCountry->need_zip_code,
            zipCodeFormat: $legacyCountry->zip_code_format,
            taxLabelDisplayed: (bool) $legacyCountry->display_tax_label,
        );
    }

    public function buildLegacyContext(): void
    {
        $this->assertArguments();

        // Only update the legacy context when the country is not the expected one, if not leave the context unchanged
        if ($this->legacyObjectNeedsUpdate($this->contextStateManager->getContext()->country, (int) $this->getLegacyCountry()->id)) {
            $this->contextStateManager->setCountry($this->getLegacyCountry());
        }
    }

    public function setCountryId(?int $countryId): self
    {
        $this->countryId = $countryId;

        return $this;
    }

    private function assertArguments(): void
    {
        if (null === $this->countryId) {
            throw new InvalidArgumentException(sprintf(
                'Cannot build Country context as no countryId has been defined you need to call %s::setCountryId to define it before building the Country context',
                self::class
            ));
        }
    }

    private function getLegacyCountry(): LegacyCountry
    {
        if ($this->legacyObjectNeedsUpdate($this->legacyCountry, $this->countryId)) {
            $this->legacyCountry = $this->countryRepository->get(new CountryId($this->countryId));
        }

        return $this->legacyCountry;
    }
}
