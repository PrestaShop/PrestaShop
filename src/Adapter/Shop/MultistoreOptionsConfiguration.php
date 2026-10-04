<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopUrlRepository;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tools;

final class MultistoreOptionsConfiguration implements DataConfigurationInterface
{
    public function __construct(
        private readonly ShopConfigurationInterface $configuration,
        private readonly ShopUrlRepository $shopUrlRepository,
    ) {
    }

    public function getConfiguration(): array
    {
        return [
            'default_shop_id' => (int) $this->configuration->get('PS_SHOP_DEFAULT', null, ShopConstraint::allShops()),
        ];
    }

    public function updateConfiguration(array $configuration): array
    {
        $this->validateConfiguration($configuration);
        $defaultShopId = new ShopId((int) $configuration['default_shop_id']);

        if (!$this->shopUrlRepository->hasBaseUrl($defaultShopId, Tools::usingSecureMode())) {
            return [[
                'key' => 'You must configure this store\'s URL before setting it as default.',
                'domain' => 'Admin.Advparameters.Notification',
                'parameters' => [],
            ]];
        }

        $this->configuration->set('PS_SHOP_DEFAULT', $defaultShopId->getValue(), ShopConstraint::allShops());

        return [];
    }

    public function validateConfiguration(array $configuration): bool
    {
        (new OptionsResolver())
            ->setRequired(['default_shop_id'])
            ->setAllowedTypes('default_shop_id', ['int', 'string'])
            ->resolve($configuration);

        return true;
    }
}
