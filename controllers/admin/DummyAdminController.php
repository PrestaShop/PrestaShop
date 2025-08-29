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

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This class is used only because some parts of the Back Office require a Controller
 * to function (like the NullDispatcher). It is also used in integration tests for override.
 */
class DummyAdminControllerCore extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->id = 0;
        $this->controller_type = 'dummy';
    }

    public function checkAccess()
    {
        return true;
    }

    public function viewAccess($disable = false)
    {
        return true;
    }

    public function postProcess()
    {
        return true;
    }

    public function display()
    {
        return '';
    }

    public function setMedia($isNewTheme = false)
    {
        return null;
    }

    public function initHeader()
    {
        return '';
    }

    public function initContent()
    {
        return '';
    }

    public function initCursedPage()
    {
        return '';
    }

    public function initFooter()
    {
        return '';
    }

    protected function redirect()
    {
        return '';
    }

    protected function buildContainer(): ContainerInterface
    {
        return SymfonyContainer::getInstance();
    }
}
