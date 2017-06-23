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
 * State
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_country", columns={"id_country"}), @ORM\Index(name="name", columns={"name"}), @ORM\Index(name="id_zone", columns={"id_zone"})})
 * @ORM\Entity
 */
class State
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_country", type="integer", nullable=false)
     */
    private $idCountry;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_zone", type="integer", nullable=false)
     */
    private $idZone;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="iso_code", type="string", length=7, nullable=false)
     */
    private $isoCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="tax_behavior", type="smallint", nullable=false)
     */
    private $taxBehavior = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_state", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idState;



    /**
     * Set idCountry
     *
     * @param integer $idCountry
     *
     * @return State
     */
    public function setIdCountry($idCountry)
    {
        $this->idCountry = $idCountry;

        return $this;
    }

    /**
     * Get idCountry
     *
     * @return integer
     */
    public function getIdCountry()
    {
        return $this->idCountry;
    }

    /**
     * Set idZone
     *
     * @param integer $idZone
     *
     * @return State
     */
    public function setIdZone($idZone)
    {
        $this->idZone = $idZone;

        return $this;
    }

    /**
     * Get idZone
     *
     * @return integer
     */
    public function getIdZone()
    {
        return $this->idZone;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return State
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
     * Set isoCode
     *
     * @param string $isoCode
     *
     * @return State
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = $isoCode;

        return $this;
    }

    /**
     * Get isoCode
     *
     * @return string
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * Set taxBehavior
     *
     * @param integer $taxBehavior
     *
     * @return State
     */
    public function setTaxBehavior($taxBehavior)
    {
        $this->taxBehavior = $taxBehavior;

        return $this;
    }

    /**
     * Get taxBehavior
     *
     * @return integer
     */
    public function getTaxBehavior()
    {
        return $this->taxBehavior;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return State
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Get idState
     *
     * @return integer
     */
    public function getIdState()
    {
        return $this->idState;
    }
}
