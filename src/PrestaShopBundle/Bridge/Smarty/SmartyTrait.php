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

namespace PrestaShopBundle\Bridge\Smarty;

use Symfony\Component\HttpFoundation\Response;

/**
 * This trait adds methods to get a response object with the HTML passed as parameters and with all stuff needed,
 * like header, footer, notifications.
 *
 * Developers must use this trait in a controller migrated horizontally to render a smarty template as a Symfony response.
 * He can also be used to add CSS, js, jquery plugin, and jquery UI to a response.
 */
trait SmartyTrait
{
    /**
     * @param string $content
     * @param Response|null $response
     *
     * @return Response
     */
    public function renderSmarty(string $content, Response $response = null, bool $isNewTheme = false): Response
    {
        $controllerBridge = $this->getLegacyControllerBridge();
        $controllerBridge->setMedia($isNewTheme);

        return $this
            ->get('prestashop.core.bridge.smarty_bridge')
            ->render($content, $controllerBridge->getConfiguration(), $response)
        ;
    }
}
