<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Import\Handler;

use PrestaShop\PrestaShop\Core\Import\Exception\NotSupportedImportTypeException;
use PrestaShop\PrestaShop\Core\Import\Handler\ImportHandlerFinderInterface;
use PrestaShop\PrestaShop\Core\Import\Handler\ImportHandlerInterface;

/**
 * Class ImportHandlerFinder is responsible for finding a proper import handler.
 */
final class ImportHandlerFinder implements ImportHandlerFinderInterface
{
    /**
     * @var ImportHandlerInterface[]
     */
    private $importHandlers;

    /**
     * @param ImportHandlerInterface[] ...$importHandlers
     */
    public function __construct(ImportHandlerInterface ...$importHandlers)
    {
        $this->importHandlers = $importHandlers;
    }

    /**
     * {@inheritdoc}
     */
    public function find($importEntityType)
    {
        foreach ($this->importHandlers as $importHandler) {
            if ($importHandler->supports($importEntityType)) {
                return $importHandler;
            }
        }

        throw new NotSupportedImportTypeException();
    }
}
