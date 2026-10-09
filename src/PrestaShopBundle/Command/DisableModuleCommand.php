<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Command;

final class DisableModuleCommand extends AbstractModuleActionCommand
{
    protected function getAction(): string
    {
        return 'disable';
    }
}
