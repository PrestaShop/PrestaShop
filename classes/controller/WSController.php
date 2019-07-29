<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\PrestaShop\Adapter\ContainerBuilder;

class WSControllerCore extends Controller
{
    /**
     * WSControllerCore constructor.
     *
     * @throws PrestaShopException
     */
    public function __construct()
    {
        parent::__construct();

        $this->controller_type = 'ws';

        $this->init();
    }

    /**
     * @return bool|void
     *
     * @throws Exception
     */
    public function checkAccess()
    {
        throw new Exception('Method checkAccess() not implemented in WSController');
    }

    /**
     * @return bool|void
     *
     * @throws Exception
     */
    public function viewAccess()
    {
        throw new Exception('Method viewAccess() not implemented in WSController');
    }

    /**
     * @return bool|ObjectModel|void
     *
     * @throws Exception
     */
    public function postProcess()
    {
        throw new Exception('Method postProcess() not implemented in WSController');
    }

    /**
     * @throws Exception
     */
    public function display()
    {
        throw new Exception('Method display() not implemented in WSController');
    }

    /**
     * @throws Exception
     */
    public function setMedia()
    {
        throw new Exception('Method setMedia() not implemented in WSController');
    }

    /**
     * @throws Exception
     */
    public function initHeader()
    {
        throw new Exception('Method initHeader() not implemented in WSController');
    }

    /**
     * @throws Exception
     */
    public function initContent()
    {
        throw new Exception('Method initContent() not implemented in WSController');
    }

    /**
     * @throws Exception
     */
    public function initCursedPage()
    {
        throw new Exception('Method initCursedPage() not implemented in WSController');
    }

    /**
     * @throws Exception
     */
    public function initFooter()
    {
        throw new Exception('Method initFooter() not implemented in WSController');
    }

    /**
     * @throws Exception
     */
    protected function redirect()
    {
        throw new Exception('Method redirect() not implemented in WSController');
    }

    /**
     * {@inheritdoc}
     */
    protected function buildContainer()
    {
        return ContainerBuilder::getContainer('ws', _PS_MODE_DEV_);
    }
}
