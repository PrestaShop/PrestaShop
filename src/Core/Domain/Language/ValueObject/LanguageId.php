<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;

/**
 * Stores language's identity
 */
class LanguageId
{
    /**
     * @var int
     */
    private $id;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->assertIsIntegerGreaterThanZero($id);

        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    private function assertIsIntegerGreaterThanZero($id)
    {
        if (!is_int($id) || 0 >= $id) {
            throw new LanguageException(sprintf('Invalid language id %s provided', var_export($id, true)));
        }
    }
}
