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

namespace PrestaShopBundle\Twig\Component;

use Doctrine\ORM\NoResultException;
use Media;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Translation\TranslatorInterface;
use PrestaShopBundle\Twig\Layout\MenuBuilder;
use Shop;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Tools;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/header.html.twig')]
class Header
{
    public string $meta_title;
    public ?string $shop_context;
    public bool $display_header_javascript;
    public array $css_files;
    public array $js_files;
    public array $js_inline;
    public ?string $displayBackOfficeHeader;

    public function __construct(
        private readonly TabRepository $tabRepository,
        private readonly LegacyContext $context,
        private readonly Configuration $configuration,
        private readonly MenuBuilder $menuBuilder,
        private readonly string $psVersion,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getEmployeeToken(): string
    {
        return Tools::getAdminToken('AdminEmployees');
    }

    public function getJsDef(): array
    {
        return Media::getJsDef();
    }

    public function getPsVersion(): string
    {
        return $this->psVersion;
    }

    public function getIsoUser(): string
    {
        return $this->context->getLanguage()->getIsoCode();
    }

    public function getCountryIsoCode(): string
    {
        return $this->context->getContext()->country->iso_code;
    }

    public function getLangIsRtl(): bool
    {
        return (bool) $this->context->getLanguage()->isRTL();
    }

    public function getShopName(): string
    {
        return $this->configuration->get('PS_SHOP_NAME');
    }

    public function getControllerName(): string
    {
        return $this->menuBuilder->getLegacyControllerClassName();
    }

    public function getImgDir(): string
    {
        return $this->context->getContext()->shop->getBaseURI() . 'img/';
    }

    public function getFullLanguageCode(): string
    {
        return $this->context->getLanguage()->getLanguageCode();
    }

    public function getFullCldrLanguageCode(): string
    {
        return $this->context->getContext()->getCurrentLocale()->getCode();
    }

    public function getRoundMode(): int
    {
        return (int) $this->configuration->get('PS_PRICE_ROUND_MODE');
    }

    public function getLegacyToken(): string
    {
        $controllerName = $this->menuBuilder->getLegacyControllerClassName();

        $tabId = '';
        if (!empty($controllerName)) {
            try {
                $tabId = $this->tabRepository->getIdByClassName($controllerName);
            } catch (NoResultException) {
            }
        }

        $employeeId = '';
        if ($this->context->getContext()->employee) {
            $employeeId = (int) $this->context->getContext()->employee->id;
        }

        return Tools::getAdminToken($controllerName . $tabId . $employeeId);
    }

    public function getDefaultLanguage(): int
    {
        return (int) $this->configuration->get('PS_LANG_DEFAULT');
    }

    public function getCurrentIndex(): string
    {
        $controllerName = $this->menuBuilder->getLegacyControllerClassName();

        return 'index.php' . (!empty($controllerName) ? '?controller=' . $controllerName : '');
    }

    public function getEditForLabel(): string
    {
        if (Shop::getContext() === Shop::CONTEXT_SHOP) {
            return $this->translator->trans('This field will be modified for this shop:', [], 'Admin.Notifications.Info')
                . sprintf('<b>%s</b>', $this->getShopName());
        } elseif (Shop::getContext() === Shop::CONTEXT_GROUP) {
            return $this->translator->trans('This field will be modified for all shops in this shop group:', [], 'Admin.Notifications.Info')
                . sprintf('<b>%s</b>', $this->getShopName());
        }

        return $this->translator->trans('This field will be modified for all your shops.', [], 'Admin.Notifications.Info');
    }
}
