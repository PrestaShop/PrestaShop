<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult;

/**
 * Carries data about single timeline item
 */
class CustomerThreadTimelineItem
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var string
     */
    private $arrow;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string|null
     */
    private $color;

    /**
     * @var int|null
     */
    private $relatedOrderId;

    /**
     * @param string $content
     * @param string $icon
     * @param string $arrow
     * @param string $date
     * @param string|null $color
     * @param int|null $relatedOrderId
     */
    public function __construct(
        $content,
        $icon,
        $arrow,
        $date,
        $color = null,
        $relatedOrderId = null
    ) {
        $this->content = $content;
        $this->icon = $icon;
        $this->arrow = $arrow;
        $this->date = $date;
        $this->color = $color;
        $this->relatedOrderId = $relatedOrderId;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getArrow()
    {
        return $this->arrow;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string|null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @return int|null
     */
    public function getRelatedOrderId()
    {
        return $this->relatedOrderId;
    }
}
