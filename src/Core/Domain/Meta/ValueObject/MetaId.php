<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaException;

/**
 * Class MetaId is responsible for providing id of meta entity.
 */
class MetaId
{
    /**
     * @var int
     */
    private $id;

    /**
     * @param int $metaId
     *
     * @throws MetaException
     */
    public function __construct($metaId)
    {
        $this->assertIsIntAndLargerThanZero($metaId);

        $this->id = $metaId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->id;
    }

    /**
     * @param int $metaId
     *
     * @throws MetaException
     */
    public function assertIsIntAndLargerThanZero($metaId)
    {
        if (!is_int($metaId) || $metaId <= 0) {
            throw new MetaException(sprintf('Invalid meta id: %s. It must be of type integer and above 0', var_export($metaId, true)));
        }
    }
}
