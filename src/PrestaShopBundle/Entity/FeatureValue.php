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
 * FeatureValue
 *
 * @ORM\Table(indexes={@ORM\Index(name="feature", columns={"id_feature"})})
 * @ORM\Entity
 */
class FeatureValue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_feature", type="integer", nullable=false)
     */
    private $idFeature;

    /**
     * @var boolean
     *
     * @ORM\Column(name="custom", type="boolean", nullable=true)
     */
    private $custom;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_feature_value", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idFeatureValue;



    /**
     * Set idFeature
     *
     * @param integer $idFeature
     *
     * @return FeatureValue
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
     * Set custom
     *
     * @param boolean $custom
     *
     * @return FeatureValue
     */
    public function setCustom($custom)
    {
        $this->custom = $custom;

        return $this;
    }

    /**
     * Get custom
     *
     * @return boolean
     */
    public function getCustom()
    {
        return $this->custom;
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
}
