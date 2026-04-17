<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Action;

use PrestaShop\PrestaShop\Core\Data\AbstractTypedCollection;

class ActionsBarButtonsCollection extends AbstractTypedCollection
{
    /**
     * {@inheritdoc}
     */
    protected function getType()
    {
        return ActionsBarButtonInterface::class;
    }
}
