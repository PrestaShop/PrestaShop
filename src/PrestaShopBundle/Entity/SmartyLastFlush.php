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
 * SmartyLastFlush
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SmartyLastFlush
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_flush", type="datetime", nullable=false)
     */
    private $lastFlush = '0000-00-00 00:00:00';

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $type;



    /**
     * Set lastFlush
     *
     * @param \DateTime $lastFlush
     *
     * @return SmartyLastFlush
     */
    public function setLastFlush($lastFlush)
    {
        $this->lastFlush = $lastFlush;

        return $this;
    }

    /**
     * Get lastFlush
     *
     * @return \DateTime
     */
    public function getLastFlush()
    {
        return $this->lastFlush;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
