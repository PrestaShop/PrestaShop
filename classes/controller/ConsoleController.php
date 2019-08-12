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

/**
 * Controller used in Console environment.
 */
class ConsoleControllerCore extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->id = 0;
        $this->controller_type = 'console';
    }

    /**
     * {@inheritdoc}
     */
    public function checkAccess()
    {
        // TODO: Implement checkAccess() method.
    }

    /**
     * {@inheritdoc}
     */
    public function viewAccess()
    {
        // TODO: Implement viewAccess() method.
    }

    /**
     * {@inheritdoc}
     */
    public function postProcess()
    {
        // TODO: Implement postProcess() method.
    }

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function setMedia()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function initHeader()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function initContent()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function initCursedPage()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function initFooter()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function redirect()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function buildContainer()
    {
        // @todo: Should we return the back office container here ?
        return null;
    }
}
