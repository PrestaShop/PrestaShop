<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Configuration;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface ImportConfigFactoryInterface defines an import configuration factory.
 *
 * @deprecated since 9.3, will be removed in the next major version - replaced by \PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext
 */
interface ImportConfigFactoryInterface
{
    /**
     * Build import configuration VO out of form data.
     *
     * @param Request $request
     *
     * @return ImportConfigInterface
     */
    public function buildFromRequest(Request $request): ImportConfigInterface;
}
