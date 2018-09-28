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

namespace PrestaShop\PrestaShop\Adapter\Meta\CommandHandler;

use Meta;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\EditMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\CommandHandler\EditMetaHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\CannotEditMetaException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaNotFoundException;
use PrestaShopException;

/**
 * Class EditMetaHandler is responsible for editing meta data,
 */
final class EditMetaHandler implements EditMetaHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws MetaNotFoundException
     * @throws CannotEditMetaException
     */
    public function handle(EditMetaCommand $command)
    {
        try {
            $entity = new Meta($command->getMetaId()->getValue());

            if (0 >= $entity->id) {
                throw new MetaNotFoundException(
                    sprintf('Meta with id "%s" was not found for edit', $command->getMetaId()->getValue())
                );
            }

            $entity->page = $command->getPageName();
            $entity->url_rewrite = $command->getRewriteUrl();

            if (null !== $command->getPageTitle()) {
                $entity->title = $command->getPageTitle();
            }

            if (null !== $command->getMetaDescription()) {
                $entity->description = $command->getMetaDescription();
            }

            if (null !== $command->getMetaKeywords()) {
                $entity->keywords = $command->getMetaKeywords();
            }

            if (false === $entity->update()) {
                throw new CannotEditMetaException(
                    sprintf(
                        'Error occurred when updating Meta with id "%s"',
                        $command->getMetaId()->getValue()
                    )
                );
            }
        } catch (PrestaShopException $e) {
            throw new CannotEditMetaException(
                sprintf(
                    'Error occurred when updating Meta with id "%s"',
                    $command->getMetaId()->getValue()
                )
            );
        }
    }
}
