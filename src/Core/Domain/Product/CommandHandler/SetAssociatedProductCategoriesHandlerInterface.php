<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\SetAssociatedProductCategoriesCommand;

/**
 * Defines contract for AssignProductToCategoriesHandler
 */
interface SetAssociatedProductCategoriesHandlerInterface
{
    /**
     * @param SetAssociatedProductCategoriesCommand $command
     */
    public function handle(SetAssociatedProductCategoriesCommand $command): void;
}
