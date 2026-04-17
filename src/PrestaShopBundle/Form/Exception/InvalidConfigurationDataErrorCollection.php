<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Exception;

use PrestaShop\PrestaShop\Core\Data\AbstractTypedCollection;

class InvalidConfigurationDataErrorCollection extends AbstractTypedCollection
{
    protected function getType(): string
    {
        return InvalidConfigurationDataError::class;
    }
}
