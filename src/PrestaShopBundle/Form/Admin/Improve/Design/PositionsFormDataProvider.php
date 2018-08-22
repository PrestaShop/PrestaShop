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

namespace PrestaShopBundle\Form\Admin\Improve\Design;

use Module;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use PrestaShop\PrestaShop\Adapter\Database;
use PrestaShopBundle\Exception\HookModuleNotFoundException;
use PrestaShop\PrestaShop\Adapter\Improve\Design\PositionsConfiguration;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * This class is responsible of managing the data manipulated for modules hooks
 * in "Improve > Design > Positions" page.
 */
final class PositionsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var PositionsConfiguration
     */
    private $configuration;

    /**
     * @var Database
     */
    private $databaseAdapterxs;

    public function __construct(
        PositionsConfiguration $configuration,
        Database $databaseAdapter
    ) {
        $this->configuration = $configuration;
        $this->databaseAdapter = $databaseAdapter;
    }

    /**
     * Load data.
     *
     * @param $moduleId Module id
     * @param $hookId   Hook id
     */
    public function load($moduleId, $hookId)
    {
        $sql = sprintf(
            'SELECT id_module FROM %shook_module WHERE id_module = %d AND id_hook = %d AND id_shop IN(%s)',
            _DB_PREFIX_,
            $moduleId,
            $hookId,
            implode(', ', ShopContext::getContextListShopID())
        );

        if (!$this->databaseAdapter->getValue($sql)) {
            throw new HookModuleNotFoundException();
        }

        $module = Module::getInstanceById($moduleId);
        $exceptionsList = $module->getExceptions($hookId, true);
        $exceptionsString = '';
        if (!empty($exceptionsList)) {
            $exceptionsString = implode(', ', current($exceptionsList));
        }

        $this->data = [
            'exceptions_text' => $exceptionsString,
            'exceptions_list' => $exceptionsList,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->configuration->updateConfiguration($data['module']);
    }
}
