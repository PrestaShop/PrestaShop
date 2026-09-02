<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult;

/**
 * Carries data for customer thread timeline
 */
class CustomerThreadTimeline
{
    /**
     * @var CustomerThreadTimelineItem[]
     */
    private $timelineItems;

    /**
     * @param CustomerThreadTimelineItem[] $timelineItems
     */
    public function __construct(array $timelineItems)
    {
        $this->timelineItems = $timelineItems;
    }

    /**
     * @return CustomerThreadTimelineItem[]
     */
    public function getTimelineItems()
    {
        return $this->timelineItems;
    }
}
