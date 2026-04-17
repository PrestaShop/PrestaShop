<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Extension;

use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class MultiStoreUrlExtension is responsible for providing a way to generate url with setShopContext parameter.
 */
class MultistoreUrlExtension extends AbstractExtension
{
    public const SHOP_CONTEXT_PARAMETER = 'setShopContext';

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('multistore_url', [$this, 'generateUrl']),
            new TwigFunction('multistore_group_url', [$this, 'generateGroupUrl']),
            new TwigFunction('multistore_shop_url', [$this, 'generateShopUrl']),
        ];
    }

    /**
     * Generate URL from current request for a specific shop group.
     *
     * @param int|null $id
     * @param string|null $prefix
     *
     * @return string
     */
    public function generateUrl(?int $id = null, ?string $prefix = null): string
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $currentRequest->query->set(
            static::SHOP_CONTEXT_PARAMETER,
            $prefix . $id
        );

        return $currentRequest->getBaseUrl()
            . $currentRequest->getPathInfo()
            . '?'
            . http_build_query($currentRequest->query->all());
    }

    /**
     * Generate URL from current request for a specific shop group.
     *
     * @param ShopGroup $group
     *
     * @return string
     */
    public function generateGroupUrl(ShopGroup $group): string
    {
        return $this->generateUrl($group->getId(), 'g-');
    }

    /**
     * Generate URL from current request for a specific shop.
     *
     * @param Shop $shop
     *
     * @return string
     */
    public function generateShopUrl(?Shop $shop = null): string
    {
        return $this->generateUrl($shop->getId(), 's-');
    }
}
