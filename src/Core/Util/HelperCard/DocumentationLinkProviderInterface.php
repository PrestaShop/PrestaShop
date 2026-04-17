<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Util\HelperCard;

/**
 * Interface DocumentationLinkProviderInterface
 */
interface DocumentationLinkProviderInterface
{
    /**
     * @param string $cardType
     *
     * @return string Link for documentation
     */
    public function getLink($cardType);
}
