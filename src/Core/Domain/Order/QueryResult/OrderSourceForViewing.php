<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use DateTimeImmutable;

class OrderSourceForViewing
{
    /**
     * @var string
     */
    private $httpReferer;

    /**
     * @var string
     */
    private $requestUri;

    /**
     * @var DateTimeImmutable
     */
    private $addedAt;

    /**
     * @var string
     */
    private $keywords;

    /**
     * @param string $httpReferer
     * @param string $requestUri
     * @param DateTimeImmutable $addedAt
     * @param string $keywords
     */
    public function __construct(string $httpReferer, string $requestUri, DateTimeImmutable $addedAt, string $keywords)
    {
        $this->httpReferer = $httpReferer;
        $this->requestUri = $requestUri;
        $this->addedAt = $addedAt;
        $this->keywords = $keywords;
    }

    /**
     * @return string
     */
    public function getHttpReferer(): string
    {
        return $this->httpReferer;
    }

    /**
     * @return string
     */
    public function getRequestUri(): string
    {
        return $this->requestUri;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getAddedAt(): DateTimeImmutable
    {
        return $this->addedAt;
    }

    /**
     * @return string
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }
}
