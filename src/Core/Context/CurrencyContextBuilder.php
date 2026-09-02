<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

use Currency as LegacyCurrency;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

class CurrencyContextBuilder implements LegacyContextBuilderInterface
{
    use LegacyObjectCheckerTrait;

    private ?int $currencyId = null;

    private ?LegacyCurrency $legacyCurrency = null;

    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
        private readonly ContextStateManager $contextStateManager,
        private readonly LanguageContext $languageContext
    ) {
    }

    public function build(): CurrencyContext
    {
        $this->assertArguments();
        $legacyCurrency = $this->getLegacyCurrency();

        $languageId = $this->languageContext->getId();
        $localizedNames = $legacyCurrency->getLocalizedNames();
        $localizedPatterns = $legacyCurrency->getLocalizedPatterns();
        $localizedSymbols = $legacyCurrency->getLocalizedSymbols();

        return new CurrencyContext(
            (int) $legacyCurrency->id,
            $localizedNames[$languageId] ?? reset($localizedNames),
            $legacyCurrency->getLocalizedNames(),
            $legacyCurrency->iso_code,
            $legacyCurrency->numeric_iso_code,
            (string) $legacyCurrency->getConversionRate(),
            $localizedSymbols[$languageId] ?? reset($localizedSymbols),
            $legacyCurrency->getLocalizedSymbols(),
            (int) $legacyCurrency->precision,
            $localizedPatterns[$languageId] ?? reset($localizedPatterns),
            $legacyCurrency->getLocalizedPatterns()
        );
    }

    public function buildLegacyContext(): void
    {
        $this->assertArguments();
        // Only update the legacy context when the currency is not the expected one, if not leave the context unchanged
        if ($this->legacyObjectNeedsUpdate($this->contextStateManager->getContext()->currency, (int) $this->getLegacyCurrency()->id)) {
            $this->contextStateManager->setCurrency($this->getLegacyCurrency());
        }
    }

    public function setCurrencyId(int $currencyId)
    {
        $this->currencyId = $currencyId;

        return $this;
    }

    private function assertArguments(): void
    {
        if (null === $this->currencyId) {
            throw new InvalidArgumentException(sprintf(
                'Cannot build Currency context as no currencyId has been defined you need to call %s::setCurrencyId to define it before building the Currency context',
                self::class
            ));
        }
    }

    private function getLegacyCurrency(): LegacyCurrency
    {
        if ($this->legacyObjectNeedsUpdate($this->legacyCurrency, $this->currencyId)) {
            $this->legacyCurrency = $this->currencyRepository->get(new CurrencyId($this->currencyId));
        }

        return $this->legacyCurrency;
    }
}
