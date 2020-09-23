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

class DummyControllerCore extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->id = 0;
        $this->controller_type = 'dummy';
    }

    /**
     * {@inheritdoc}
     */
    public function checkAccess()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function viewAccess()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function postProcess()
    {
        return null;
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
        return SymfonyContainer::getInstance();
    }
}
