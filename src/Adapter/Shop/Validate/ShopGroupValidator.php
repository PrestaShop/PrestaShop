<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\Validate;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotUpdateShopGroupException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopGroupConstraintException;
use ShopGroup;

final class ShopGroupValidator extends AbstractObjectModelValidator
{
    public function __construct(
        private readonly ShopRepository $shopRepository,
    ) {
    }

    public function validate(ShopGroup $shopGroup): void
    {
        $this->validateObjectModelProperty($shopGroup, 'name', ShopGroupConstraintException::class, ShopGroupConstraintException::INVALID_NAME);
        $this->validateObjectModelProperty($shopGroup, 'color', ShopGroupConstraintException::class, ShopGroupConstraintException::INVALID_COLOR);

        if ($shopGroup->share_order && (!$shopGroup->share_customer || !$shopGroup->share_stock)) {
            throw new ShopGroupConstraintException(
                'Orders can only be shared when both customers and stock are shared',
                ShopGroupConstraintException::SHARE_ORDER_REQUIRES_SHARED_CUSTOMERS_AND_STOCK
            );
        }
    }

    /**
     * @param array<string, bool> $previousSharingOptions
     * @param array<string, bool> $sharingOptions
     *
     * @throws CannotUpdateShopGroupException
     */
    public function assertSharingOptionsCanChange(array $previousSharingOptions, array $sharingOptions): void
    {
        if ($sharingOptions !== $previousSharingOptions && $this->shopRepository->countActiveShops() > 1) {
            throw new CannotUpdateShopGroupException(
                'Sharing options cannot be changed once there is more than one shop',
                CannotUpdateShopGroupException::SHARING_OPTIONS_LOCKED
            );
        }
    }
}
