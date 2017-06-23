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
 * SearchIndex
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_product", columns={"id_product"})})
 * @ORM\Entity
 */
class SearchIndex
{
    /**
     * @var integer
     *
     * @ORM\Column(name="weight", type="smallint", nullable=false)
     */
    private $weight = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_word", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idWord;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_product", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $idProduct;



    /**
     * Set weight
     *
     * @param integer $weight
     *
     * @return SearchIndex
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set idWord
     *
     * @param integer $idWord
     *
     * @return SearchIndex
     */
    public function setIdWord($idWord)
    {
        $this->idWord = $idWord;

        return $this;
    }

    /**
     * Get idWord
     *
     * @return integer
     */
    public function getIdWord()
    {
        return $this->idWord;
    }

    /**
     * Set idProduct
     *
     * @param integer $idProduct
     *
     * @return SearchIndex
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
