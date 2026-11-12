<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPage\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;

/**
 * Class which holds the cms page id value.
 */
class CmsPageId
{
    private int $cmsPageId;

    /**
     * @throws CmsPageException
     */
    public function __construct(int $cmsPageId)
    {
        $this->assertIsIntegerGreaterThanZero($cmsPageId);
        $this->cmsPageId = $cmsPageId;
    }

    public function getValue(): int
    {
        return $this->cmsPageId;
    }

    /**
     * Validates that the value is integer and is greater than zero.
     *
     * @throws CmsPageException
     */
    private function assertIsIntegerGreaterThanZero(int $cmsPageId): void
    {
        if (0 >= $cmsPageId) {
            throw new CmsPageException(sprintf('Invalid cms page id %s supplied', var_export($cmsPageId, true)));
        }
    }
}
