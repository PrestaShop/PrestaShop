<?php

namespace PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\QueryResult;

use PrestaShop\PrestaShop\Adapter\Entity\Product;

/** @todo at this point duplicate of PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductCustomizationForViewing */
class OrderDetailCustomization
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
        /** @todo change const */
        if (0 === $this->type) {
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
