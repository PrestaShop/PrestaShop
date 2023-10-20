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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SearchEngine\CommandHandler;

use PrestaShop\PrestaShop\Adapter\SearchEngine\AbstractSearchEngineHandler;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Command\AddSearchEngineCommand;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\CommandHandler\AddSearchEngineHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception\SearchEngineException;
use PrestaShop\PrestaShop\Core\Domain\SearchEngine\ValueObject\SearchEngineId;
use PrestaShopException;
use SearchEngine;

/**
 * Handles command what is responsible for creating new search engine.
 */
final class AddSearchEngineHandler extends AbstractSearchEngineHandler implements AddSearchEngineHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SearchEngineException
     */
    public function handle(AddSearchEngineCommand $command): SearchEngineId
    {
        $searchEngine = new SearchEngine();

        $searchEngine->server = $command->getServer();
        $searchEngine->getvar = $command->getQueryKey();

        try {
            if (false === $searchEngine->validateFields(false)) {
                throw new SearchEngineException('Search engine contain invalid field values');
            }

            if (false === $searchEngine->add()) {
                throw new SearchEngineException(sprintf('Failed to add new search engine "%s"', $command->getServer()));
            }
        } catch (PrestaShopException $e) {
            throw new SearchEngineException(sprintf('Failed to add new search engine "%s"', $command->getServer()));
        }

        return new SearchEngineId((int) $searchEngine->id);
    }
}
