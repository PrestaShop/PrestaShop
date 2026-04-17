<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use PrestaShop\PrestaShop\Adapter\Entity\Product;

class OrderProductCustomizationForViewing
{
    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $image;

    /**
     * @param int $type
     * @param string $name
     * @param string $value
     */
    public function __construct(int $type, string $name, string $value)
    {
        $this->type = $type;
        $this->name = $name;
        $this->value = $value;
        if (Product::CUSTOMIZE_FILE === $this->type) {
            $this->image = _THEME_PROD_PIC_DIR_ . $this->value . '_small';
        }
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }
}
