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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

use Currency as LegacyCurrency;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

class CurrencyContextBuilder implements LegacyContextBuilderInterface
{
    private ?int $currencyId = null;

    private ?LegacyCurrency $legacyCurrency = null;

    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
        private readonly ContextStateManager $contextStateManager,
        private readonly ConfigurationInterface $configuration
    ) {
    }

    public function build(): CurrencyContext
    {
        $this->assertArguments();
        $legacyCurrency = $this->getLegacyCurrency();
        // Temporary solution, this should be fetched from the LanguageContext when it's available
        $languageId = $this->configuration->get('PS_LANG_DEFAULT');

        $localizedNames = $legacyCurrency->getLocalizedNames();
        $localizedPatterns = $legacyCurrency->getLocalizedPatterns();
        $localizedSymbols = $legacyCurrency->getLocalizedSymbols();

        return new CurrencyContext(
            $legacyCurrency->id,
            $localizedNames[$languageId] ?? reset($localizedNames),
            $legacyCurrency->getLocalizedNames(),
            $legacyCurrency->iso_code,
            $legacyCurrency->numeric_iso_code,
            (string) $legacyCurrency->getConversionRate(),
            $localizedSymbols[$languageId] ?? reset($localizedSymbols),
            $legacyCurrency->getLocalizedSymbols(),
            $legacyCurrency->precision,
            $localizedPatterns[$languageId] ?? reset($localizedPatterns),
            $legacyCurrency->getLocalizedPatterns()
        );
    }

    public function buildLegacyContext(): void
    {
        $this->assertArguments();
        $this->contextStateManager->setCurrency($this->getLegacyCurrency());
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
        if (!$this->legacyCurrency) {
            $this->legacyCurrency = $this->currencyRepository->get(new CurrencyId($this->currencyId));
        }

        return $this->legacyCurrency;
    }
}
