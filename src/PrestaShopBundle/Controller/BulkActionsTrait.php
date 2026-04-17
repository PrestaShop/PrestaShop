<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

trait BulkActionsTrait
{
    protected function getBulkActionIds(Request $request, string $key): array
    {
        $ids = $request->request->all($key);

        foreach ($ids as $i => $id) {
            $ids[$i] = (int) $id;
        }

        sort($ids);

        return $ids;
    }
}
