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
 * Risk
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Risk
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="percent", type="boolean", nullable=false)
     */
    private $percent;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=32, nullable=true)
     */
    private $color;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_risk", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idRisk;



    /**
     * Set percent
     *
     * @param boolean $percent
     *
     * @return Risk
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * Get percent
     *
     * @return boolean
     */
    public function getPercent()
    {
        return $this->percent;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return Risk
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Get idRisk
     *
     * @return integer
     */
    public function getIdRisk()
    {
        return $this->idRisk;
    }
}
