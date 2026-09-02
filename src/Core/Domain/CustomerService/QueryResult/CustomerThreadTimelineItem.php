<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
