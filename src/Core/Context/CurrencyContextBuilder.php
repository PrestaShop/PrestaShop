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

use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

class CurrencyContextBuilder
{
    private ?int $currencyId = null;

    public function __construct(
        private readonly CurrencyRepository $currencyRepository,
        private readonly LegacyContext $legacyContext,
        private readonly ContextStateManager $contextStateManager
    ) {
    }

    public function build(): CurrencyContext
    {
        if (null === $this->currencyId) {
            throw new InvalidArgumentException(sprintf(
                'Cannot build Currency context as no currencyId has been defined you need to call %s::setCurrencyId to define it before building the Currency context',
                self::class
            ));
        }

        $legacyCurrency = $this->currencyRepository->get(new CurrencyId($this->currencyId));

        $currency = new Currency(
            $legacyCurrency->id,
            $legacyCurrency->getName(),
            $legacyCurrency->getLocalizedNames(),
            $legacyCurrency->iso_code,
            $legacyCurrency->iso_code_num,
            $legacyCurrency->numeric_iso_code,
            $legacyCurrency->getConversionRate(),
            (bool) $legacyCurrency->deleted,
            (bool) $legacyCurrency->unofficial,
            (bool) $legacyCurrency->modified,
            (bool) $legacyCurrency->active,
            $legacyCurrency->getSign(),
            $legacyCurrency->getSymbol(),
            $legacyCurrency->getLocalizedSymbols(),
            $legacyCurrency->format,
            $legacyCurrency->blank,
            $legacyCurrency->decimals,
            $legacyCurrency->precision,
            $legacyCurrency->pattern,
            $legacyCurrency->getLocalizedPatterns()
        );

        return new CurrencyContext($currency);
    }

    public function setCurrencyId(int $currencyId)
    {
        $this->currencyId = $currencyId;

        return $this;
    }

    public function buildLegacyContext(): self
    {
        // set currency object model in legacy context if currency is different
        if (empty($this->legacyContext->getContext()->currency)
            || (int) $this->legacyContext->getContext()->currency->id !== $this->currencyId
        ) {
            $legacyCurrency = $this->currencyRepository->get(new CurrencyId($this->currencyId));
            $this->contextStateManager->setCurrency($legacyCurrency);
        }

        return $this;
    }
}
