<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Routing;

class LegacyControllerConstants
{
    public const CONTROLLER_CLASS_ATTRIBUTE = '_legacy_controller_class';
    public const CONTROLLER_NAME_ATTRIBUTE = '_legacy_controller_name';
    public const CONTROLLER_ACTION_ATTRIBUTE = '_legacy_controller_action';
    public const INSTANCE_ATTRIBUTE = '_legacy_controller_instance';
    public const IS_MODULE_ATTRIBUTE = '_legacy_controller_is_module';
    public const MULTISHOP_CONTEXT_ATTRIBUTE = '_legacy_controller_is_all_shop_context';
}
