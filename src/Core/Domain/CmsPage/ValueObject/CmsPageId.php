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
    /**
     * @var int
     */
    private $cmsPageId;

    /**
     * @param int $cmsPageId
     *
     * @throws CmsPageException
     */
    public function __construct($cmsPageId)
    {
        $this->assertIsIntegerGreaterThanZero($cmsPageId);
        $this->cmsPageId = $cmsPageId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->cmsPageId;
    }

    /**
     * Validates that the value is integer and is greater than zero.
     *
     * @param int $cmsPageId
     *
     * @throws CmsPageException
     */
    private function assertIsIntegerGreaterThanZero($cmsPageId)
    {
        if (!is_int($cmsPageId) || 0 >= $cmsPageId) {
            throw new CmsPageException(sprintf('Invalid cms page id %s supplied', var_export($cmsPageId, true)));
        }
    }
}
