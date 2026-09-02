<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Module\SourceHandler;

class SourceHandlerFactory
{
    /** @var SourceHandlerInterface[] */
    private $sourceHandlers = [];

    /**
     * @param SourceHandlerInterface[] $handlers
     */
    public function __construct(iterable $handlers = [])
    {
        foreach ($handlers as $handler) {
            $this->addHandler($handler);
        }
    }

    public function addHandler(SourceHandlerInterface $sourceHandler): void
    {
        $this->sourceHandlers[] = $sourceHandler;
    }

    public function getHandler($source): SourceHandlerInterface
    {
        foreach ($this->sourceHandlers as $handler) {
            if ($handler->canHandle($source)) {
                return $handler;
            }
        }

        throw new SourceHandlerNotFoundException(sprintf('Handler not found for source %s', $source));
    }
}
