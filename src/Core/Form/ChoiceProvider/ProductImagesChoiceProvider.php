<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Query\GetProductImages;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImage;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;

final class ProductImagesChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var int
     */
    private $defaultShopId;

    /**
     * @var int|null
     */
    private $contextShopId;

    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(
        CommandBusInterface $queryBus,
        int $defaultShopId,
        ?int $contextShopId
    ) {
        $this->queryBus = $queryBus;
        $this->defaultShopId = $defaultShopId;
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritDoc}
     */
    public function getChoices(array $options): array
    {
        if (empty($options['product_id'])) {
            return [];
        }

        $shopConstraint = null !== $this->contextShopId ? ShopConstraint::shop($this->contextShopId) : ShopConstraint::shop($this->defaultShopId);
        /** @var ProductImage[] $productImages */
        $productImages = $this->queryBus->handle(new GetProductImages((int) $options['product_id'], $shopConstraint));

        $choices = [];
        foreach ($productImages as $productImage) {
            $choices[$productImage->getThumbnailUrl()] = $productImage->getImageId();
        }

        return $choices;
    }
}
