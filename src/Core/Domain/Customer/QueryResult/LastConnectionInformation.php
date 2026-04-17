<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Class LastConnectionInformation holds information about last customer connection to shop.
 */
class LastConnectionInformation
{
    /**
     * @var int
     */
    private $connectionId;

    /**
     * @var string
     */
    private $connectionDate;

    /**
     * @var int
     */
    private $pagesViewed;

    /**
     * @var string
     */
    private $totalTime;

    /**
     * @var string
     */
    private $httpReferer;

    /**
     * @var string
     */
    private $ipAddress;

    /**
     * @param int $connectionId
     * @param string $connectionDate
     * @param int $pagesViewed
     * @param string $totalTime
     * @param string $httpReferer
     * @param string $ipAddress
     */
    public function __construct(
        $connectionId,
        $connectionDate,
        $pagesViewed,
        $totalTime,
        $httpReferer,
        $ipAddress
    ) {
        $this->connectionId = $connectionId;
        $this->connectionDate = $connectionDate;
        $this->pagesViewed = $pagesViewed;
        $this->totalTime = $totalTime;
        $this->httpReferer = $httpReferer;
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return int
     */
    public function getConnectionId()
    {
        return $this->connectionId;
    }

    /**
     * @return string
     */
    public function getConnectionDate()
    {
        return $this->connectionDate;
    }

    /**
     * @return int
     */
    public function getPagesViewed()
    {
        return $this->pagesViewed;
    }

    /**
     * @return string
     */
    public function getTotalTime()
    {
        return $this->totalTime;
    }

    /**
     * @return string
     */
    public function getHttpReferer()
    {
        return $this->httpReferer;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }
}
