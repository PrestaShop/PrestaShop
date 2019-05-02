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

namespace PrestaShopBundle\Controller\Admin;

use DateTime;
use PrestaShopBundle\Form\Admin\Login\ForgotPasswordType;
use PrestaShopBundle\Form\Admin\Login\LoginType;

/**
 * Class LoginController responsible for employee login page.
 */
class LoginController extends FrameworkBundleAdminController
{
    public function indexAction()
    {
        $languageDataProvider = $this->get('prestashop.adapter.data_provider.language');
        $loginForm = $this->createForm(LoginType::class);
        $forgotPasswordForm = $this->createForm(ForgotPasswordType::class);

        return $this->render('@PrestaShop/Admin/Login/index.html.twig', [
            'loginForm' => $loginForm->createView(),
            'forgotPasswordForm' => $forgotPasswordForm->createView(),
            'shopName' => $this->configuration->get('PS_SHOP_NAME'),
            'prestashopVersion' => $this->configuration->get('_PS_VERSION_'),
            'imgDir' => $this->configuration->get('_PS_IMG_'),
            'languageIso' => $languageDataProvider->getLanguageIsoById(
                $this->configuration->get('PS_LANG_DEFAULT')
            ),
            'currentYear' => (new DateTime())->format('Y'),
        ]);
    }
}
