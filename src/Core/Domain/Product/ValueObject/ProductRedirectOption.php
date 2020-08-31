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

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductRedirectionSettings;

/**
 * Holds values for product redirect option.
 */
class ProductRedirectOption
{
    /**
     * @var string
     */
    protected $redirectType;

    /**
     * @var int
     */
    protected $redirectTargetId;

    /**
     * @param string $redirectType
     * @param int $redirectTargetId
     *
     * @throws ProductConstraintException
     */
    public function __construct(string $redirectType, int $redirectTargetId)
    {
        $this->assertRedirectType($redirectType);
        $this->assertTargetIdIsGreaterThanZero($redirectTargetId);
        $this->redirectType = $redirectType;
        $this->redirectTargetId = $redirectTargetId;
    }

    /**
     * @return string
     */
    public function getRedirectType(): string
    {
        return $this->redirectType;
    }

    /**
     * @return int
     */
    public function getRedirectTargetId(): int
    {
        return $this->redirectTargetId;
    }

    /**
     * @param string $value
     *
     * @throws ProductConstraintException
     */
    protected function assertRedirectType(string $value): void
    {
        if (in_array($value, ProductRedirectionSettings::AVAILABLE_REDIRECT_TYPES)) {
            return;
        }

        throw new ProductConstraintException(
            sprintf(
                'Invalid product redirect type "%s". Allowed types are: "%s"',
                $value,
                implode(',', ProductRedirectionSettings::AVAILABLE_REDIRECT_TYPES)
            ),
            ProductConstraintException::INVALID_REDIRECT_TYPE
        );
    }

    /**
     * @param int $id
     *
     * @throws ProductConstraintException
     */
    private function assertTargetIdIsGreaterThanZero(int $id): void
    {
        if ($id <= 0) {
            throw new ProductConstraintException(
                sprintf('Invalid redirect target id "%s". It must be greater than zero', $id),
                ProductConstraintException::INVALID_REDIRECT_TARGET_ID
            );
        }
    }
}
