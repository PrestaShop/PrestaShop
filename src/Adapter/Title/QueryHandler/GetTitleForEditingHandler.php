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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Title\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Title\AbstractTitleHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Title\Query\GetTitleForEditing;
use PrestaShop\PrestaShop\Core\Domain\Title\QueryHandler\GetTitleForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Title\QueryResult\EditableTitle;

/**
 * Handles command that gets title for editing
 *
 * @internal
 */
#[AsQueryHandler]
class GetTitleForEditingHandler extends AbstractTitleHandler implements GetTitleForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetTitleForEditing $query): EditableTitle
    {
        $title = $this->titleRepository->get($query->getTitleId());

        return new EditableTitle(
            $query->getTitleId()->getValue(),
            $title->name,
            (int) $title->type
        );
    }
}
