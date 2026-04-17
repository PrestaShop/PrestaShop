<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Domain;

use ObjectModel;

trait LocalizedObjectModelTrait
{
    protected function fillLocalizedValues(ObjectModel $product, string $propertyName, array $localizedValues, array &$updatableProperties): void
    {
        foreach ($localizedValues as $langId => $localizedValue) {
            $product->$propertyName[$langId] = $localizedValue;
        }
        $updatableProperties[$propertyName] = array_keys($localizedValues);
    }
}
