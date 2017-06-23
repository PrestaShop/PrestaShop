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
 * ImportMatch
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ImportMatch
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=32, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="match", type="text", length=65535, nullable=false)
     */
    private $match;

    /**
     * @var integer
     *
     * @ORM\Column(name="skip", type="integer", nullable=false)
     */
    private $skip;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_import_match", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idImportMatch;



    /**
     * Set name
     *
     * @param string $name
     *
     * @return ImportMatch
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
     * Set match
     *
     * @param string $match
     *
     * @return ImportMatch
     */
    public function setMatch($match)
    {
        $this->match = $match;

        return $this;
    }

    /**
     * Get match
     *
     * @return string
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * Set skip
     *
     * @param integer $skip
     *
     * @return ImportMatch
     */
    public function setSkip($skip)
    {
        $this->skip = $skip;

        return $this;
    }

    /**
     * Get skip
     *
     * @return integer
     */
    public function getSkip()
    {
        return $this->skip;
    }

    /**
     * Get idImportMatch
     *
     * @return integer
     */
    public function getIdImportMatch()
    {
        return $this->idImportMatch;
    }
}
