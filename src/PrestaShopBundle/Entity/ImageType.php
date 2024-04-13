<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\ImageTypeRepository")
 * @ORM\Table()
 * @UniqueEntity("name")
 */
class ImageType
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id_image_type", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, unique=true)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="width", type="integer")
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(name="height", type="integer")
     */
    private $height;

    /**
     * @var bool
     *
     * @ORM\Column(name="products", type="boolean")
     */
    private $products;

    /**
     * @var bool
     *
     * @ORM\Column(name="categories", type="boolean")
     */
    private $categories;

    /**
     * @var bool
     *
     * @ORM\Column(name="manufacturers", type="boolean")
     */
    private $manufacturers;

    /**
     * @var bool
     *
     * @orm\column(name="suppliers", type="boolean")
     */
    private $suppliers;

    /**
     * @var bool
     *
     * @orm\column(name="stores", type="boolean")
     */
    private $stores;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function isProducts(): bool
    {
        return $this->products;
    }

    public function setProducts(bool $products): self
    {
        $this->products = $products;

        return $this;
    }

    public function isCategories(): bool
    {
        return $this->categories;
    }

    public function setCategories(bool $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    public function isManufacturers(): bool
    {
        return $this->manufacturers;
    }

    public function setManufacturers(bool $manufacturers): self
    {
        $this->manufacturers = $manufacturers;

        return $this;
    }

    public function isSuppliers(): bool
    {
        return $this->suppliers;
    }

    public function setSuppliers(bool $suppliers): self
    {
        $this->suppliers = $suppliers;

        return $this;
    }

    public function isStores(): bool
    {
        return $this->stores;
    }

    public function setStores(bool $stores): self
    {
        $this->stores = $stores;

        return $this;
    }
}
