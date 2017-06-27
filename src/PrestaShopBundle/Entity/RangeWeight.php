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
 * RangeWeight
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="id_carrier", columns={"id_carrier", "delimiter1", "delimiter2"})})
 * @ORM\Entity
 */
class RangeWeight
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_carrier", type="integer", nullable=false)
     */
    private $idCarrier;

    /**
     * @var string
     *
     * @ORM\Column(name="delimiter1", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $delimiter1;

    /**
     * @var string
     *
     * @ORM\Column(name="delimiter2", type="decimal", precision=20, scale=6, nullable=false)
     */
    private $delimiter2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_range_weight", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idRangeWeight;



    /**
     * Set idCarrier
     *
     * @param integer $idCarrier
     *
     * @return RangeWeight
     */
    public function setIdCarrier($idCarrier)
    {
        $this->idCarrier = $idCarrier;

        return $this;
    }

    /**
     * Get idCarrier
     *
     * @return integer
     */
    public function getIdCarrier()
    {
        return $this->idCarrier;
    }

    /**
     * Set delimiter1
     *
     * @param string $delimiter1
     *
     * @return RangeWeight
     */
    public function setDelimiter1($delimiter1)
    {
        $this->delimiter1 = $delimiter1;

        return $this;
    }

    /**
     * Get delimiter1
     *
     * @return string
     */
    public function getDelimiter1()
    {
        return $this->delimiter1;
    }

    /**
     * Set delimiter2
     *
     * @param string $delimiter2
     *
     * @return RangeWeight
     */
    public function setDelimiter2($delimiter2)
    {
        $this->delimiter2 = $delimiter2;

        return $this;
    }

    /**
     * Get delimiter2
     *
     * @return string
     */
    public function getDelimiter2()
    {
        return $this->delimiter2;
    }

    /**
     * Get idRangeWeight
     *
     * @return integer
     */
    public function getIdRangeWeight()
    {
        return $this->idRangeWeight;
    }
}
