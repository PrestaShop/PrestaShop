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
 * FeatureProduct
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_feature_value", columns={"id_feature_value"}), @ORM\Index(name="id_product", columns={"id_product"})})
 * @ORM\Entity
 */
class FeatureProduct
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_feature_value", type="integer", nullable=false)
     */
    private $idFeatureValue;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_feature", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idFeature;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProduct;



    /**
     * Set idFeatureValue
     *
     * @param integer $idFeatureValue
     *
     * @return FeatureProduct
     */
    public function setIdFeatureValue($idFeatureValue)
    {
        $this->idFeatureValue = $idFeatureValue;

        return $this;
    }

    /**
     * Get idFeatureValue
     *
     * @return integer
     */
    public function getIdFeatureValue()
    {
        return $this->idFeatureValue;
    }

    /**
     * Set idFeature
     *
     * @param integer $idFeature
     *
     * @return FeatureProduct
     */
    public function setIdFeature($idFeature)
    {
        $this->idFeature = $idFeature;

        return $this;
    }

    /**
     * Get idFeature
     *
     * @return integer
     */
    public function getIdFeature()
    {
        return $this->idFeature;
    }

    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return FeatureProduct
     */
    public function setIdProduct($idProduct)
    {
        $this->idProduct = $idProduct;

        return $this;
    }

    /**
     * Get idProduct
     *
     * @return integer
     */
    public function getIdProduct()
    {
        return $this->idProduct;
    }
}
