<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Import\Command\ImportCsvFromFileCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Result\ImportResult;

/**
 * Defines the contract for running a CSV import through the façade command.
 *
 * The interface stays stable across the import migration: today the concrete
 * handler wires the existing Importer / legacy controller, later stories will
 * dispatch the ImportRun aggregate behind the very same method.
 *
 * Assumed deviation from the usual "commands return void/an id" rule: this command
 * intentionally returns an ImportResult. The report (errors/warnings/notices/counts)
 * is the façade's contract — it is what the safety-net scenarios assert against and
 * what the UI needs back from a run — so it is returned synchronously here.
 */
interface ImportCsvFromFileHandlerInterface
{
    public function handle(ImportCsvFromFileCommand $command): ImportResult;
}
