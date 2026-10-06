<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Command;

class UninstallModuleCommand extends AbstractModuleActionCommand
{
    protected function getAction(): string
    {
        return 'uninstall';
    }
}
