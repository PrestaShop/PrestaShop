<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Zone\Command;

/**
 * Adds new zone with provided data.
 */
class AddZoneCommand
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var array
     */
    private $shopAssociation;

    /**
     * @param string $name
     * @param bool $enabled
     * @param array $shopAssociation
     */
    public function __construct(string $name, bool $enabled, array $shopAssociation)
    {
        $this->name = $name;
        $this->enabled = $enabled;
        $this->shopAssociation = $shopAssociation;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @return array
     */
    public function getShopAssociation(): array
    {
        return $this->shopAssociation;
    }
}
