<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Gets, for each "customer service" contact category, the number of open
 * threads and the oldest one still waiting for a reply. This powers the
 * per-category panels (e.g. "Webmaster", "Customer service") displayed
 * above the customer thread listing.
 */
class GetCustomerServiceContactCategoriesStatistics
{
    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @var ShopConstraint
     */
    private $shopConstraint;

    public function __construct(int $languageId, ShopConstraint $shopConstraint)
    {
        $this->languageId = new LanguageId($languageId);
        $this->shopConstraint = $shopConstraint;
    }

    public function getLanguageId(): LanguageId
    {
        return $this->languageId;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }
}
