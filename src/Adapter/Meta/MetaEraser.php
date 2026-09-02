<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Meta;

use Meta;
use PrestaShopCollection;
use PrestaShopException;

/**
 * Class MetaEraser is responsible for removing data from meta entity.
 */
final class MetaEraser
{
    /**
     * Erases data from meta entity.
     *
     * @param array $metaIds
     *
     * @return array
     *
     * @throws PrestaShopException
     */
    public function erase(array $metaIds)
    {
        $errors = [];

        if (empty($metaIds)) {
            $errors[] = [
                'key' => 'You must select at least one element to delete.',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ];

            return $errors;
        }

        $metaData = new PrestaShopCollection(Meta::class);
        $metaData->where('id_meta', 'in', $metaIds);

        /** @var Meta $item */
        foreach ($metaData->getResults() as $item) {
            if (!$item->delete()) {
                $errors[] = [
                    'key' => 'Can\'t delete #%id%',
                    'parameters' => [
                        '%id%' => $item->id,
                    ],
                    'domain' => 'Admin.Notifications.Error',
                ];

                continue;
            }
        }

        return $errors;
    }
}
