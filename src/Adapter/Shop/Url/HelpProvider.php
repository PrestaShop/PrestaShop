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

namespace PrestaShop\PrestaShop\Adapter\Shop\Url;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Shop\Url\UrlProviderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Generates the help url which is used most in the help sidebar component.
 */
class HelpProvider implements UrlProviderInterface
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $version;

    public function __construct(
        LegacyContext $legacyContext,
        TranslatorInterface $translator,
        RouterInterface $router,
        string $version
    ) {
        $this->legacyContext = $legacyContext;
        $this->translator = $translator;
        $this->router = $router;
        $this->version = $version;
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl(string $section = '', string $title = '')
    {
        if (empty($title)) {
            $title = $this->translator->trans('Help', [], 'Admin.Global');
        }

        $docLink = urlencode('https://help.prestashop.com/' . $this->legacyContext->getEmployeeLanguageIso() . '/doc/'
            . $section . '?version=' . $this->version . '&country=' . $this->legacyContext->getEmployeeLanguageIso());

        return $this->router->generate('admin_common_sidebar', [
            'url' => $docLink,
            'title' => $title,
        ]);
    }
}
