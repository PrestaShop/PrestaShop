<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Configuration;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface ImportRuntimeConfigFactoryInterface describes an import runtime config factory.
 */
interface ImportRuntimeConfigFactoryInterface
{
    /**
     * Build runtime config object out of request.
     *
     * @param Request $request
     *
     * @return ImportRuntimeConfigInterface
     */
    public function buildFromRequest(Request $request);
}
