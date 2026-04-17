<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search;

class SearchPanel implements SearchPanelInterface
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $buttonLabel;

    /**
     * @var string
     */
    protected $link;

    /**
     * @var array
     */
    protected $queryParams;
    /**
     * @var bool|null
     */
    private $isExternalLink;

    public function __construct(
        string $title,
        string $buttonLabel,
        string $link,
        array $queryParams,
        ?bool $isExternalLink = true
    ) {
        $this->title = $title;
        $this->buttonLabel = $buttonLabel;
        $this->link = $link;
        $this->queryParams = $queryParams;
        $this->isExternalLink = $isExternalLink;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getButtonLabel(): string
    {
        return $this->buttonLabel;
    }

    public function getLink(): string
    {
        return sprintf('%s?%s', $this->link, http_build_query($this->queryParams));
    }

    public function isExternalLink(): bool
    {
        return $this->isExternalLink;
    }
}
