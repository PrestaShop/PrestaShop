<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\ImageTypeRepository")
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"name"})})
 *
 * @UniqueEntity({"name"})
 */
class ImageType
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_image_type", type="integer", options={"unsigned": true})
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\Column(name="name", type="string", length=64, unique=true)
     */
    private string $name;

    /**
     * @ORM\Column(name="width", type="integer", options={"unsigned": true})
     */
    private int $width;

    /**
     * @ORM\Column(name="height", type="integer", options={"unsigned": true})
     */
    private int $height;

    /**
     * @ORM\Column(name="products", type="boolean", options={"default": 1})
     */
    private bool $products;

    /**
     * @ORM\Column(name="categories", type="boolean", options={"default": 1})
     */
    private bool $categories;

    /**
     * @ORM\Column(name="manufacturers", type="boolean", options={"default": 1})
     */
    private bool $manufacturers;

    /**
     * @ORM\Column(name="suppliers", type="boolean", options={"default": 1})
     */
    private bool $suppliers;

    /**
     * @ORM\Column(name="stores", type="boolean", options={"default": 1})
     */
    private bool $stores;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function isProducts(): bool
    {
        return $this->products;
    }

    public function setProducts(bool $products): static
    {
        $this->products = $products;

        return $this;
    }

    public function isCategories(): bool
    {
        return $this->categories;
    }

    public function setCategories(bool $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    public function isManufacturers(): bool
    {
        return $this->manufacturers;
    }

    public function setManufacturers(bool $manufacturers): static
    {
        $this->manufacturers = $manufacturers;

        return $this;
    }

    public function isSuppliers(): bool
    {
        return $this->suppliers;
    }

    public function setSuppliers(bool $suppliers): static
    {
        $this->suppliers = $suppliers;

        return $this;
    }

    public function isStores(): bool
    {
        return $this->stores;
    }

    public function setStores(bool $stores): static
    {
        $this->stores = $stores;

        return $this;
    }
}
