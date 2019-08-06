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

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use function in_array;

/**
 * Holds information about product redirection page when product is not available,
 */
class OfflineRedirectionPage
{
    /**
     * @var string - 302 moved temporarily to product page
     */
    public const TEMPORARY_TO_PRODUCT_TYPE = 'temporary_to_product';

    /**
     * @var string - 301 status code moved permanent to product page
     */
    public const PERMANENT_TO_PRODUCT_TYPE = 'permanent_to_product';

    /**
     * @var string - 302 moved temporarily to category page
     */
    public const TEMPORARY_TO_CATEGORY_TYPE = 'temporary_to_category';

    /**
     * @var string - 301 status code moved permanent to product page
     */
    public const PERMANENT_TO_CATEGORY_TYPE = 'permanent_to_category';

    /**
     * @var string - no redirect action is performed. 404 status code.
     */
    public const NO_REDIRECTION_TYPE = 'none';

    /**
     * @var array
     */
    public const AVAILABLE_REDIRECTION_TYPES = [
        self::TEMPORARY_TO_PRODUCT_TYPE,
        self::PERMANENT_TO_PRODUCT_TYPE,
        self::TEMPORARY_TO_CATEGORY_TYPE,
        self::PERMANENT_TO_CATEGORY_TYPE,
        self::NO_REDIRECTION_TYPE,
    ];

    /**
     * @var string
     */
    private $redirectionType;

    /**
     * @var int|null
     */
    private $resourceId;

    /**
     * @param string $redirectionType
     * @param int|null $resourceId
     *
     * @throws ProductConstraintException
     */
    public function __construct(
        string $redirectionType,
        ?int $resourceId
    ) {
        $this->assertIsValidRedirectionType($redirectionType);
        $this->assertIsValidResourceId($redirectionType, $resourceId);

        $this->redirectionType = $redirectionType;
        $this->resourceId = $resourceId;
    }

    /**
     * @return string
     */
    public function getRedirectionType(): string
    {
        return $this->redirectionType;
    }

    /**
     * @return int|null
     */
    public function getResourceId(): ?int
    {
        return $this->resourceId;
    }

    /**
     * @param string $redirectionType
     *
     * @throws ProductConstraintException
     */
    private function assertIsValidRedirectionType(string $redirectionType): void
    {
        if (!in_array($redirectionType, self::AVAILABLE_REDIRECTION_TYPES, true)) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid redicrection type "%s" detected. Available values are "%s"',
                    $redirectionType,
                    implode(',', self::AVAILABLE_REDIRECTION_TYPES)
                ),
                ProductConstraintException::INVALID_REDIRECTION_TYPE
            );
        }
    }

    /**
     * @param string $redirectionType
     * @param int|null $resourceId
     *
     * @throws ProductConstraintException
     */
    private function assertIsValidResourceId(string $redirectionType, ?int $resourceId): void
    {
        if (self::NO_REDIRECTION_TYPE !== $redirectionType && null === $resourceId) {
            throw new ProductConstraintException(
                sprintf(
                    'For given redirection type "%s" resource id is required',
                    $redirectionType
                ),
                ProductConstraintException::INVALID_REDIRECTION_RESOURCE_ID
            );
        }
    }
}
