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

namespace PrestaShop\PrestaShop\Core\Model;

interface CurrencyInterface
{
    public function getId(): int;

    public function getName(): string;

    public function getLocalizedNames(): array;

    public function getIsoCode(): string;

    public function getIsoCodeNum(): string;

    public function getNumericIsoCode(): ?string;

    public function getConversionRate(): float;

    public function isDeleted(): bool;

    public function isUnofficial(): bool;

    public function isModified(): bool;

    public function isActive(): bool;

    public function getSign(): string;

    public function getSymbol(): string;

    public function getLocalizedSymbols(): array;

    public function getFormat(): string;

    public function getBlank(): int;

    public function getDecimals(): int;

    public function getPrecision(): int;

    public function getPattern(): string;

    public function getLocalizedPatterns(): array;
}
