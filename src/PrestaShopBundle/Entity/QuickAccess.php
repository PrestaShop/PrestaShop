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
 * QuickAccess
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class QuickAccess
{
    /**
     * @var boolean
     *
     * @ORM\Column(name="new_window", type="boolean", nullable=false, options={"default":0})
     */
    private $newWindow = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="link", type="string", length=255, nullable=false)
     */
    private $link;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_quick_access", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idQuickAccess;



    /**
     * Set newWindow
     *
     * @param boolean $newWindow
     *
     * @return QuickAccess
     */
    public function setNewWindow($newWindow)
    {
        $this->newWindow = $newWindow;

        return $this;
    }

    /**
     * Get newWindow
     *
     * @return boolean
     */
    public function getNewWindow()
    {
        return $this->newWindow;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return QuickAccess
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Get idQuickAccess
     *
     * @return integer
     */
    public function getIdQuickAccess()
    {
        return $this->idQuickAccess;
    }
}
