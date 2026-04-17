<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Employee;

use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Employee\AvatarProviderInterface;

/**
 * Class AvatarProvider provides employee avatar data.
 */
final class AvatarProvider implements AvatarProviderInterface
{
    /**
     * @var Tools
     */
    private $tools;

    /**
     * @param Tools $tools
     */
    public function __construct(Tools $tools)
    {
        $this->tools = $tools;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAvatarUrl()
    {
        return $this->tools->getAdminImageUrl('pr/default.jpg');
    }
}
