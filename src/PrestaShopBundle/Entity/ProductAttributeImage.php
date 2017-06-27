<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductAttributeImage
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_image", columns={"id_image"})})
 * @ORM\Entity
 */
class ProductAttributeImage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_product_attribute", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProductAttribute;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_image", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idImage;



    /**
     * Set idProductAttribute
     *
     * @param integer $idProductAttribute
     *
     * @return ProductAttributeImage
     */
    public function setIdProductAttribute($idProductAttribute)
    {
        $this->idProductAttribute = $idProductAttribute;

        return $this;
    }

    /**
     * Get idProductAttribute
     *
     * @return integer
     */
    public function getIdProductAttribute()
    {
        return $this->idProductAttribute;
    }

    /**
     * Set idImage
     *
     * @param integer $idImage
     *
     * @return ProductAttributeImage
     */
    public function setIdImage($idImage)
    {
        $this->idImage = $idImage;

        return $this;
    }

    /**
     * Get idImage
     *
     * @return integer
     */
    public function getIdImage()
    {
        return $this->idImage;
    }
}
