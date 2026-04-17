<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\Url;

use Link;
use PrestaShop\PrestaShop\Core\Shop\Url\UrlProviderInterface;
use Tools;

class ProductPreviewProvider implements UrlProviderInterface
{
    /**
     * @var Link
     */
    protected $link;

    /**
     * @var int
     */
    protected $employeeId;

    /**
     * @var bool
     */
    private $urlRewritingIsEnabled;

    public function __construct(
        Link $link,
        bool $urlRewritingIsEnabled,
        int $employeeId
    ) {
        $this->link = $link;
        $this->employeeId = $employeeId;
        $this->urlRewritingIsEnabled = $urlRewritingIsEnabled;
    }

    /**
     * Create a link to a product.
     *
     * @param int|null $productId
     * @param bool $active
     * @param int|null $shopId
     *
     * @return string
     */
    public function getUrl(?int $productId = null, ?bool $active = true, ?int $shopId = null): string
    {
        $preview_url = $this->link->getProductLink(
            $productId,
            null,
            null,
            null,
            null,
            $shopId,
            null,
            $this->urlRewritingIsEnabled
        );

        if (!$active) {
            $token = Tools::getAdminTokenLite('AdminProducts');
            $preview_url = sprintf(
                '%s%sadtoken=%s&id_employee=%d&preview=1',
                $preview_url,
                (!str_contains($preview_url, '?')) ? '?' : '&',
                $token,
                $this->employeeId
            );
        }

        return $preview_url;
    }
}
