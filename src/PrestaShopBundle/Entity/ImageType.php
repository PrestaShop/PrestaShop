<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImageType
 *
 * @ORM\Table(indexes={@ORM\Index(name="image_type_name", columns={"name"})})
 * @ORM\Entity
 */
class ImageType
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="width", type="integer", nullable=false)
     */
    private $width;

    /**
     * @var integer
     *
     * @ORM\Column(name="height", type="integer", nullable=false)
     */
    private $height;

    /**
     * @var boolean
     *
     * @ORM\Column(name="products", type="boolean", nullable=false)
     */
    private $products = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="categories", type="boolean", nullable=false)
     */
    private $categories = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="manufacturers", type="boolean", nullable=false)
     */
    private $manufacturers = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="suppliers", type="boolean", nullable=false)
     */
    private $suppliers = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="stores", type="boolean", nullable=false)
     */
    private $stores = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_image_type", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idImageType;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return ImageType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set width
     *
     * @param integer $width
     *
     * @return ImageType
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     *
     * @return ImageType
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set products
     *
     * @param boolean $products
     *
     * @return ImageType
     */
    public function setProducts($products)
    {
        $this->products = $products;

        return $this;
    }

    /**
     * Get products
     *
     * @return boolean
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set categories
     *
     * @param boolean $categories
     *
     * @return ImageType
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Get categories
     *
     * @return boolean
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set manufacturers
     *
     * @param boolean $manufacturers
     *
     * @return ImageType
     */
    public function setManufacturers($manufacturers)
    {
        $this->manufacturers = $manufacturers;

        return $this;
    }

    /**
     * Get manufacturers
     *
     * @return boolean
     */
    public function getManufacturers()
    {
        return $this->manufacturers;
    }

    /**
     * Set suppliers
     *
     * @param boolean $suppliers
     *
     * @return ImageType
     */
    public function setSuppliers($suppliers)
    {
        $this->suppliers = $suppliers;

        return $this;
    }

    /**
     * Get suppliers
     *
     * @return boolean
     */
    public function getSuppliers()
    {
        return $this->suppliers;
    }

    /**
     * Set stores
     *
     * @param boolean $stores
     *
     * @return ImageType
     */
    public function setStores($stores)
    {
        $this->stores = $stores;

        return $this;
    }

    /**
     * Get stores
     *
     * @return boolean
     */
    public function getStores()
    {
        return $this->stores;
    }

    /**
     * Get idImageType
     *
     * @return integer
     */
    public function getIdImageType()
    {
        return $this->idImageType;
    }
}
