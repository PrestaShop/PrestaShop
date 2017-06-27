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
 * CartRuleCombination
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_cart_rule_1", columns={"id_cart_rule_1"}), @ORM\Index(name="id_cart_rule_2", columns={"id_cart_rule_2"})})
 * @ORM\Entity
 */
class CartRuleCombination
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_cart_rule_1", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCartRule1;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cart_rule_2", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idCartRule2;



    /**
     * Set idCartRule1
     *
     * @param integer $idCartRule1
     *
     * @return CartRuleCombination
     */
    public function setIdCartRule1($idCartRule1)
    {
        $this->idCartRule1 = $idCartRule1;

        return $this;
    }

    /**
     * Get idCartRule1
     *
     * @return integer
     */
    public function getIdCartRule1()
    {
        return $this->idCartRule1;
    }

    /**
     * Set idCartRule2
     *
     * @param integer $idCartRule2
     *
     * @return CartRuleCombination
     */
    public function setIdCartRule2($idCartRule2)
    {
        $this->idCartRule2 = $idCartRule2;

        return $this;
    }

    /**
     * Get idCartRule2
     *
     * @return integer
     */
    public function getIdCartRule2()
    {
        return $this->idCartRule2;
    }
}
