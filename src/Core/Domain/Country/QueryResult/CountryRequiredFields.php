<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Country\QueryResult;

/**
 * Stores data about address fields which are required by country
 */
class CountryRequiredFields
{
    /** @var bool */
    private $stateRequired;

    /** @var bool */
    private $dniRequired;

    /**
     * @param bool $stateRequired
     * @param bool $dniRequired
     */
    public function __construct(bool $stateRequired, bool $dniRequired)
    {
        $this->stateRequired = $stateRequired;
        $this->dniRequired = $dniRequired;
    }

    /**
     * @return bool
     */
    public function isStateRequired(): bool
    {
        return $this->stateRequired;
    }

    /**
     * @return bool
     */
    public function isDniRequired(): bool
    {
        return $this->dniRequired;
    }
}
