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

use PrestaShopBundle\Twig\Component\HeadTag;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/LegacyLayout/head_tag.html.twig')]
class LegacyHeadTag extends HeadTag
{
    use LegacyControllerTrait;

    public function mount(string $metaTitle = ''): void
    {
        parent::mount($this->getLegacyMetaTitle());
    }

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
     * Legacy controller builds the meta title differently, so we match this for backward compatibility and so that the UI
     * tests can run with their expected values.
     *
     * @return string
     */
    protected function getLegacyMetaTitle(): string
    {
        $legacyMetaTitle = $this->getLegacyController()->getMetaTitle();
        if (empty($legacyMetaTitle)) {
            $breadcrumbs = $this->menuBuilder->getBreadcrumbLinks();
            if (empty($breadcrumbs)) {
                return '';
            } else {
                return $breadcrumbs['tab']->name;
            }
        }

        if (is_array($legacyMetaTitle)) {
            $legacyMetaTitle = strip_tags(implode(' ' . $this->configuration->get('PS_NAVIGATION_PIPE') . ' ', $legacyMetaTitle));
        }

        return $legacyMetaTitle;
    }
}
