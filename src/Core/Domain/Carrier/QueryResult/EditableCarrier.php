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

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\QueryResult;

/**
 * Stores carrier data that's needed for editing.
 */
class EditableCarrier
{
    public function __construct(
        private int $carrierId,
        private string $name,
        private int $grade,
        private string $trackingUrl,
        private int $position,
        private bool $active,
        /** @var string[] $delay */
        private array $delay,
        private int $max_width,
        private int $max_height,
        private int $max_depth,
        private int $max_weight,
        private array $group_access
    ) {
    }

    public function getCarrierId(): int
    {
        return $this->carrierId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGrade(): int
    {
        return $this->grade;
    }

    public function getTrackingUrl(): string
    {
        return $this->trackingUrl;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDelay(): array
    {
        return $this->delay;
    }

    public function getLogoPath(): ?string
    {
        if (file_exists(_PS_SHIP_IMG_DIR_ . $this->carrierId . '.jpg')) {
            return _PS_SHIP_IMG_DIR_ . $this->carrierId . '.jpg';
        }

        return null;
    }

    public function getDelay(): array
    {
        return $this->delay;
    }

    public function getMaxWidth(): int
    {
        return $this->max_width;
    }

    public function getMaxHeight(): int
    {
        return $this->max_height;
    }

    public function getMaxDepth(): int
    {
        return $this->max_depth;
    }

    public function getMaxWeight(): int
    {
        return $this->max_weight;
    }

    public function getGroupAccess(): array
    {
        return $this->group_access;
    }
}
