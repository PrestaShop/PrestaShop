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
 * SpecificPricePriority
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="id_product", columns={"id_product"})})
 * @ORM\Entity
 */
class SpecificPricePriority
{
    /**
     * @var string
     *
     * @ORM\Column(name="priority", type="string", length=80, nullable=false)
     */
    private $priority;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_specific_price_priority", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idSpecificPricePriority;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProduct;



    /**
     * Set priority
     *
     * @param string $priority
     *
     * @return SpecificPricePriority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set idSpecificPricePriority
     *
     * @param integer $idSpecificPricePriority
     *
     * @return SpecificPricePriority
     */
    public function setIdSpecificPricePriority($idSpecificPricePriority)
    {
        $this->idSpecificPricePriority = $idSpecificPricePriority;

        return $this;
    }

    /**
     * Get idSpecificPricePriority
     *
     * @return integer
     */
    public function getIdSpecificPricePriority()
    {
        return $this->idSpecificPricePriority;
    }

    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return SpecificPricePriority
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
