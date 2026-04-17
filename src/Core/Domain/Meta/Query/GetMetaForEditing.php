<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Meta\Query;

use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaException;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\MetaId;

/**
 * Class GetMetaForEditing is responsible for providing required data for GetMetaForEditingHandler to return meta data.
 */
class GetMetaForEditing
{
    /**
     * @var MetaId
     */
    private $metaId;

    /**
     * GetMetaForEditing constructor.
     *
     * @param int $metaId
     *
     * @throws MetaException
     */
    public function __construct($metaId)
    {
        $this->metaId = new MetaId($metaId);
    }

    /**
     * @return MetaId
     */
    public function getMetaId()
    {
        return $this->metaId;
    }
}
