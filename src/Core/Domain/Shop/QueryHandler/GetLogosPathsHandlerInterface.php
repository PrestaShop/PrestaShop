<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shop\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Shop\Query\GetLogosPaths;
use PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult\LogosPaths;

/**
 * Interface for service which handles GetLogos query
 */
interface GetLogosPathsHandlerInterface
{
    /**
     * @param GetLogosPaths $query
     *
     * @return LogosPaths
     */
    public function handle(GetLogosPaths $query);
}
