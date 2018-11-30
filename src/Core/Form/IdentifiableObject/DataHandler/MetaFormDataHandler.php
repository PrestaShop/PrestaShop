<?php
/**
 * 2007-2018 PrestaShop.
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
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\AddMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\EditMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\ValueObject\MetaId;

/**
 * Class MetaFormDataHandler is responsible to handle creation and update logic for meta form.
 */
final class MetaFormDataHandler implements FormDataHandlerInterface
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
        $addMetaCommand = new AddMetaCommand(
            $data['page_name'],
            $data['page_title'],
            $data['meta_description'],
            $data['meta_keywords'],
            $data['url_rewrite']
        );

        /** @var MetaId $metaId */
        $metaId = $this->commandBus->handle($addMetaCommand);

        return $metaId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($metaId, array $data)
    {
        $editMetaCommand = new EditMetaCommand(
            new MetaId($metaId),
            $data['page_name'],
            $data['url_rewrite']
        );

        /** @var MetaId $metaId */
        $metaId = $this->commandBus->handle($editMetaCommand);

        return $metaId->getValue();
    }
}
