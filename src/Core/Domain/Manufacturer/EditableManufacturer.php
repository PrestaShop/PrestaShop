<?php

/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer;

use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\VO\ManufacturerId;

class EditableManufacturer
{
    /**
     * @var ManufacturerId
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * Descriptions, indexed by language id
     * @var string[]
     */
    private $descriptions;

    /**
     * Descriptions, indexed by language id
     * @var string[]
     */
    private $shortDescriptions;

    /**
     * Meta tiles, indexed by language id
     * @var string[]
     */
    private $metaTitles;

    /**
     * Meta keywords, indexed by language id
     * @var string[]
     */
    private $metaKeywords;

    /**
     * Meta descriptions, indexed by language id
     * @var string[]
     */
    private $metaDescriptions;

    /**
     * @var bool
     */
    private $active;

    /**
     * @param ManufacturerId $manufacturerId
     * @param $name
     * @param $descriptions
     * @param array $shortDescriptions
     * @param array $metaTitles
     * @param array $metaKeywords
     * @param array $metaDescriptions
     * @param $active
     *
     * @throws ManufacturerException
     */
    public function __construct(
        ManufacturerId $manufacturerId,
        $name,
        $descriptions,
        array $shortDescriptions,
        array $metaTitles,
        array $metaKeywords,
        array $metaDescriptions,
        $active
    ) {
        $this
            ->setId($manufacturerId)
            ->setName($name)
            ->setDescriptions($descriptions)
            ->setShortDescriptions($shortDescriptions)
            ->setMetaTitles($metaTitles)
            ->setMetaKeywords($metaKeywords)
            ->setMetaDescription($metaDescriptions)
            ->setActive($active)
        ;
    }

    /**
     * @return ManufacturerId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getDescriptions()
    {
        return $this->descriptions;
    }

    /**
     * @return string[]
     */
    public function getShortDescriptions()
    {
        return $this->shortDescriptions;
    }

    /**
     * @return string[]
     */
    public function getMetaTitles()
    {
        return $this->metaTitles;
    }

    /**
     * @return string[]
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * @return string[]
     */
    public function getMetaDescriptions()
    {
        return $this->metaDescriptions;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return self
     */
    private function setActive($active)
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @param string[] $metaDescription
     *
     * @return self
     */
    private function setMetaDescription($metaDescription)
    {
        $this->metaDescriptions = $metaDescription;
        return $this;
    }

    /**
     * @param string[] $metaKeywords
     *
     * @return self
     */
    private function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
        return $this;
    }

    /**
     * @param string[] $metaTitles
     *
     * @return self
     */
    private function setMetaTitles($metaTitles)
    {
        $this->metaTitles = $metaTitles;
        return $this;
    }

    /**
     * @param string[] $shortDescriptions
     *
     * @return self
     */
    private function setShortDescriptions($shortDescriptions)
    {
        $this->shortDescriptions = $shortDescriptions;
        return $this;
    }

    /**
     * @param string[] $descriptions
     *
     * @return self
     */
    private function setDescriptions($descriptions)
    {
        $this->descriptions = $descriptions;
        return $this;
    }

    /**
     * @param ManufacturerId $id
     *
     * @return self
     */
    private function setId(ManufacturerId $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $name
     *
     * @return self
     *
     * @throws ManufacturerException
     */
    private function setName($name)
    {
        if ($name === '') {
            throw new ManufacturerException("Name cannot be empty");
        }

        $this->name = $name;
        return $this;
    }
}
