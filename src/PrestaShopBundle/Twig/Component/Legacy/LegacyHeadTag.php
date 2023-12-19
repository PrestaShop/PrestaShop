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

namespace PrestaShopBundle\Twig\Component\Legacy;

use AdminController;
use PrestaShopBundle\Twig\Component\HeadTag;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/LegacyLayout/head_tag.html.twig')]
class LegacyHeadTag extends HeadTag
{
    public function getControllerName(): string
    {
        return $this->getLegacyController()->controller_name;
    }

    public function getLegacyToken(): string
    {
        return $this->getLegacyController()->token;
    }

    public function getCurrentIndex(): string
    {
        return $this->getLegacyController()::$currentIndex;
    }

    public function getCssFiles(): array
    {
        return $this->getLegacyController()->css_files;
    }

    public function getJsFiles(): array
    {
        return $this->getLegacyController()->js_files;
    }

    /**
     * For legacy pages rendered with the symfony layout we don't use the LegacyControllerContext which purpose is to replace
     * the legacy controller for backward compatibility. In this case a real legacy controller is already accessible via the
     * legacy context and should be preferred.
     *
     * Its data, and especially the CSS, JS files and JS definitions are more likely to be up-to-date.
     *
     * @return AdminController
     */
    protected function getLegacyController(): AdminController
    {
        return $this->context->getContext()->controller;
    }
}
