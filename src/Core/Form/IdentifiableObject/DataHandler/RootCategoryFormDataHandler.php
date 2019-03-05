<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;

/**
 * Creates/updates root category from data submited in category form
 */
final class RootCategoryFormDataHandler extends AbstractCategoryFormDataHandler
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $command = new AddRootCategoryCommand(
            $data['name'],
            $data['link_rewrite'],
            $data['active']
        );

        $this->populateCommandWithData($command, $data);

        /** @var CategoryId $categoryId */
        $categoryId = $this->commandBus->handle($command);

        return $categoryId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($rootCategoryId, array $data)
    {
        $command = new EditRootCategoryCommand($rootCategoryId);

        $this->populateCommandWithData($command, $data);

        $this->commandBus->handle($command);

        return $rootCategoryId;
    }
}
