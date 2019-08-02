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
 * Defines where product should be visible.
 */
class Visibility
{
    /**
     * @var string defines that product is visible everywhere - e.g category, product, search pages
     */
    public const EVERYWHERE = 'both';

    /**
     * @var string - defines that the product is visible only in catalog page.
     */
    public const CATALOG = 'catalog';

    /**
     * @var string - defines that the product is visible only in search results.
     */
    public const SEARCH = 'search';

    /**
     * @var string - defines that product should not appear anywhere.
     */
    public const NOWHERE = 'none';

    public const AVAILABLE_VISIBILITY = [
        self::EVERYWHERE,
        self::CATALOG,
        self::SEARCH,
        self::NOWHERE,
    ];

    /**
     * @var string
     */
    private $visibility;

    /**
     * @param string $visibility
     *
     * @throws ProductConstraintException
     */
    public  function __construct(string $visibility)
    {
        $this->assertIsValidVisibility($visibility);

        $this->visibility = $visibility;
    }

    /**
     * @return string
     */
    public function getVisibility(): string
    {
        return $this->visibility;
    }

    /**
     * @param string $visibility
     *
     * @throws ProductConstraintException
     */
    private function assertIsValidVisibility(string $visibility): void
    {
        if (!in_array($visibility, self::AVAILABLE_VISIBILITY, true)) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid product visibility "%s" detected. Available values are "%s"',
                    $visibility,
                    implode(',', self::AVAILABLE_VISIBILITY)
                ),
                ProductConstraintException::INVALID_VISIBILITY_TYPE
            );
        }
    }
}
