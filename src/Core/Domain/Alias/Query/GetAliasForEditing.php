<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Alias\Query;

use PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject\AliasId;

/**
 * Class GetAliasForEditing is responsible for getting the data related with alias entity.
 */
class GetAliasForEditing
{
    /**
     * @var AliasId
     */
    private $aliasId;

    public function __construct(int $aliasId)
    {
        $this->aliasId = new AliasId($aliasId);
    }

    /**
     * @return AliasId
     */
    public function getAliasId(): AliasId
    {
        return $this->aliasId;
    }
}
