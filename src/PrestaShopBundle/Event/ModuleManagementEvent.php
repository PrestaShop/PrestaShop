<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Event;

use PrestaShop\PrestaShop\Core\Module\ModuleInterface;
use Symfony\Contracts\EventDispatcher\Event;

class ModuleManagementEvent extends Event
{
    public const PRE_ACTION = 'module.pre_action';
    public const INSTALL = 'module.install';
    public const POST_INSTALL = 'module.post.install';
    public const UNINSTALL = 'module.uninstall';
    public const DISABLE = 'module.disable';
    public const ENABLE = 'module.enable';
    public const UPGRADE = 'module.upgrade';
    public const UPLOAD = 'module.upload';
    public const RESET = 'module.reset';
    public const DELETE = 'module.delete';

    /** @var ModuleInterface */
    private $module;

    public function __construct(ModuleInterface $module)
    {
        $this->module = $module;
    }

    public function getModule(): ModuleInterface
    {
        return $this->module;
    }
}
