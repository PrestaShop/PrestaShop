<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult;

/**
 * One customer-provided value attached to a customized return product line.
 * Mirrors Product::CUSTOMIZE_FILE / CUSTOMIZE_TEXTFIELD pairs.
 */
class OrderReturnCustomizationFieldForEditing
{
    /**
     * @var int matches Product::CUSTOMIZE_FILE (0) or CUSTOMIZE_TEXTFIELD (1)
     */
    private $type;

    /**
     * @var string Raw value as stored in customized_data.value (file name or text).
     */
    private $value;

    public function __construct(int $type, string $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
