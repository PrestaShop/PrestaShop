<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\SpecificPriceConstraintException;

/**
 * Holds valid specific price priority values
 */
class PriorityList
{
    public const PRIORITY_GROUP = 'id_group';
    public const PRIORITY_CURRENCY = 'id_currency';
    public const PRIORITY_COUNTRY = 'id_country';
    public const PRIORITY_SHOP = 'id_shop';

    public const AVAILABLE_PRIORITIES = [
        'group' => self::PRIORITY_GROUP,
        'currency' => self::PRIORITY_CURRENCY,
        'country' => self::PRIORITY_COUNTRY,
        'shop' => self::PRIORITY_SHOP,
    ];

    /**
     * @var string[]
     */
    private $priorities;

    /**
     * @param string[] $priorities
     */
    public function __construct(array $priorities)
    {
        $this->assertPriorities($priorities);
        $this->priorities = $priorities;
    }

    /**
     * @return string[]
     */
    public function getPriorities(): array
    {
        return $this->priorities;
    }

    /**
     * @param string[] $priorities
     *
     * @throws SpecificPriceConstraintException
     */
    private function assertPriorities(array $priorities): void
    {
        $checkedPriorities = [];
        foreach ($priorities as $priority) {
            if (!in_array($priority, static::AVAILABLE_PRIORITIES, true)) {
                throw new SpecificPriceConstraintException(
                    sprintf('Invalid priority value "%s"', $priority),
                    SpecificPriceConstraintException::INVALID_PRIORITY
                );
            }

            if (in_array($priority, $checkedPriorities)) {
                throw new SpecificPriceConstraintException(
                    'Invalid priorities. Priorities cannot duplicate.',
                    SpecificPriceConstraintException::DUPLICATE_PRIORITY
                );
            }

            $checkedPriorities[] = $priority;
        }
    }
}
