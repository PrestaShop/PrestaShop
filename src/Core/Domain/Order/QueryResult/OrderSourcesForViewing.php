<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

class OrderSourcesForViewing
{
    /** @var OrderSourceForViewing[] */
    private $sources = [];

    /**
     * @param OrderSourceForViewing[] $sources
     */
    public function __construct(array $sources)
    {
        foreach ($sources as $source) {
            $this->addSource($source);
        }
    }

    /**
     * @return OrderSourceForViewing[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * @param OrderSourceForViewing $source
     */
    private function addSource(OrderSourceForViewing $source): void
    {
        $this->sources[] = $source;
    }
}
