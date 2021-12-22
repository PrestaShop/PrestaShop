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

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use Context;
use Db;
use Employee;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;
use PrestaShop\PrestaShop\Core\Image\ImageTypeRepository;
use PrestaShop\PrestaShop\Core\Module\HookConfigurator;
use PrestaShop\PrestaShop\Core\Module\HookRepository;
use Shop;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class ThemeManagerBuilder
{
    private $context;
    private $db;
    private $themeValidator;

    public function __construct(Context $context, Db $db, ThemeValidator $themeValidator = null)
    {
        $this->context = $context;
        $this->db = $db;
        $this->themeValidator = $themeValidator;
    }

    public function build()
    {
        $configuration = new Configuration();
        $configuration->restrictUpdatesTo($this->context->shop);
        if (null === $this->themeValidator) {
            $this->themeValidator = new ThemeValidator($this->context->getTranslator(), new Configuration());
        }
        if (null === $this->context->employee) {
            $this->context->employee = new Employee();
        }

        return new ThemeManager(
            $this->context->shop,
            $configuration,
            $this->themeValidator,
            $this->context->getTranslator(),
            $this->context->employee,
            new Filesystem(),
            new Finder(),
            new HookConfigurator(
                new HookRepository(
                    new HookInformationProvider(),
                    $this->context->shop,
                    $this->db
                )
            ),
            $this->buildRepository($this->context->shop),
            new ImageTypeRepository(
                $this->context->shop,
                $this->db
            )
        );
    }

    public function buildRepository(Shop $shop = null)
    {
        if (!$shop instanceof Shop) {
            $shop = $this->context->shop;
        }

        $configuration = new Configuration();
        $configuration->restrictUpdatesTo($shop);

        return new ThemeRepository(
            $configuration,
            new Filesystem(),
            $shop
        );
    }
}
