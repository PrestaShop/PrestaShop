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
 * ReferrerCache
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ReferrerCache
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_connections_source", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idConnectionsSource;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_referrer", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idReferrer;



    /**
     * Set idConnectionsSource
     *
     * @param integer $idConnectionsSource
     *
     * @return ReferrerCache
     */
    public function setIdConnectionsSource($idConnectionsSource)
    {
        $this->idConnectionsSource = $idConnectionsSource;

        return $this;
    }

    /**
     * Get idConnectionsSource
     *
     * @return integer
     */
    public function getIdConnectionsSource()
    {
        return $this->idConnectionsSource;
    }

    /**
     * Set idReferrer
     *
     * @param integer $idReferrer
     *
     * @return ReferrerCache
     */
    public function setIdReferrer($idReferrer)
    {
        $this->idReferrer = $idReferrer;

        return $this;
    }

    /**
     * Get idReferrer
     *
     * @return integer
     */
    public function getIdReferrer()
    {
        return $this->idReferrer;
    }
}
