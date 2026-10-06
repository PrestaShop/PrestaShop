<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Command;

class UpgradeModuleCommand extends AbstractModuleActionCommand
{
    protected function getAction(): string
    {
        return 'upgrade';
    }
}
