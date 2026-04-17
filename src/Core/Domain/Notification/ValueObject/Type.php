<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Notification\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Notification\Exception\NotificationException;

/**
 * Notifications types
 */
class Type
{
    public const ORDER = 'order';

    public const CUSTOMER = 'customer';

    public const CUSTOMER_MESSAGE = 'customer_message';

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     *
     * @throws NotificationException
     */
    public function __construct(string $type)
    {
        $this->assertIsValidType($type);

        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @throws NotificationException
     */
    private function assertIsValidType(string $type)
    {
        $allowedTypes = [self::ORDER, self::CUSTOMER, self::CUSTOMER_MESSAGE];
        if (!in_array($type, $allowedTypes)) {
            throw new NotificationException(sprintf('Type %s is invalid. Supported types are: %s', var_export($type, true), implode(', ', $allowedTypes)));
        }
    }
}
