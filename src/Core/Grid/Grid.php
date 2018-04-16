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

namespace PrestaShop\PrestaShop\Core\Grid;

use PrestaShop\PrestaShop\Core\Grid\Configuration\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Grid\DataSource\DataSourceInterface;

final class Grid implements GridInterface
{
    private $name;
    private $configuration;
    private $source;

    public function __construct($name, ConfigurationInterface $configuration, DataSourceInterface $source)
    {
        $this->name = $name;
        $this->configuration = $configuration;
        $this->source = $source;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $this->configuration->setName($this->name);

        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->source->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function getDataSource()
    {
        return $this->source;
    }
}