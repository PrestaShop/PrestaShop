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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\LocalizedTags;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Updates product tags in provided languages
 */
class SetProductTagsCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var LocalizedTags[]
     */
    private $localizedTagsList;

    /**
     * @param int $productId
     * @param array $localizedTags
     */
    public function __construct(int $productId, array $localizedTags)
    {
        $this->productId = new ProductId($productId);
        $this->setLocalizedTagsList($localizedTags);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return LocalizedTags[]
     */
    public function getLocalizedTagsList(): array
    {
        return $this->localizedTagsList;
    }

    /**
     * @param array[] $localizedTags key-value pairs where each key represents language id and value is the array of tags
     *
     * @throws ProductConstraintException|InvalidArgumentException
     */
    private function setLocalizedTagsList(array $localizedTags): void
    {
        if (empty($localizedTags)) {
            throw new InvalidArgumentException(sprintf(
                'Empty array of product tags provided in %s. To remove all product tags use %s.',
                self::class,
                RemoveAllProductTagsCommand::class
            ));
        }

        foreach ($localizedTags as $langId => $tags) {
            $this->localizedTagsList[] = new LocalizedTags($langId, $tags);
        }
    }
}
