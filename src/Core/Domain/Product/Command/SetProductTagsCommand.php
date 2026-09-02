<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
