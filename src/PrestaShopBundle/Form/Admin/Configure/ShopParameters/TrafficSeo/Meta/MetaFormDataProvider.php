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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\SaveMetaCommand;

/**
 * Class MetaFormDataProvider is responsible for providing data for Shop parameters ->
 * Traffic & Seo -> Seo & Urls -> add/edit form.
 */
class MetaFormDataProvider
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * MetaFormDataProvider constructor.
     *
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function getData($metaId)
    {
        // TODO: Implement getData() method.
    }


    public function saveData(array $data)
    {
        $this->commandBus->handle($this->getSaveMetaCommand($data));
        return [];
    }

    /**
     * Gets save meta command.
     *
     * @param array $data
     *
     * @return SaveMetaCommand
     */
    private function getSaveMetaCommand(array $data)
    {
        return new SaveMetaCommand(
            $data['page_name'],
            $data['page_title'],
            $data['meta_description'],
            (array) $data['meta_keywords'], //todo: remove casting once multilang value is fixed.
            $data['url_rewrite']
        );
    }
}
