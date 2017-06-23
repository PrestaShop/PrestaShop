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
 * SearchEngine
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SearchEngine
{
    /**
     * @var string
     *
     * @ORM\Column(name="server", type="string", length=64, nullable=false)
     */
    private $server;

    /**
     * @var string
     *
     * @ORM\Column(name="getvar", type="string", length=16, nullable=false)
     */
    private $getvar;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_search_engine", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSearchEngine;



    /**
     * Set server
     *
     * @param string $server
     *
     * @return SearchEngine
     */
    public function setServer($server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Get server
     *
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Set getvar
     *
     * @param string $getvar
     *
     * @return SearchEngine
     */
    public function setGetvar($getvar)
    {
        $this->getvar = $getvar;

        return $this;
    }

    /**
     * Get getvar
     *
     * @return string
     */
    public function getGetvar()
    {
        return $this->getvar;
    }

    /**
     * Get idSearchEngine
     *
     * @return integer
     */
    public function getIdSearchEngine()
    {
        return $this->idSearchEngine;
    }
}
