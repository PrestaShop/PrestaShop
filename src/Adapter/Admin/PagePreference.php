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

namespace PrestaShop\PrestaShop\Adapter\Admin;

use AppKernel;
use Db;
use PrestaShopBundle\Service\TransitionalBehavior\AdminPagePreferenceInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * Adapter to know which page's version to display.
 *
 * This implementation gives methods to use to take decision:
 * - if we should display the new refactored page, or the old legacy one.
 * - if we should display the switch on the admin layout to change this setting.
 *
 * Data is stored in the cookie, as legacy does.
 */
class PagePreference implements AdminPagePreferenceInterface
{
    /**
     * @var bool
     */
    private $isDebug;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session, bool $isDebug = _PS_MODE_DEV_)
    {
        if ($session->isStarted()) {
            $this->session = $session;
        } else {
            $sessionClass = get_class($session);
            $this->session = new $sessionClass(new PhpBridgeSessionStorage());
        }
        $this->isDebug = $isDebug;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemporaryShouldUseLegacyPage($page)
    {
        if (!$page) {
            throw new InvalidParameterException('$page parameter missing');
        }

        return $this->session->has('should_use_legacy_page_for_' . $page) && $this->session->get('should_use_legacy_page_for_' . $page, 0) == 1;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemporaryShouldUseLegacyPage($page, $useLegacy)
    {
        if (!$page) {
            throw new InvalidParameterException('$page parameter missing');
        }

        if ((bool) $useLegacy) {
            $this->session->set('should_use_legacy_page_for_' . $page, 1);
        } else {
            $this->session->remove('should_use_legacy_page_for_' . $page);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTemporaryShouldAllowUseLegacyPage($page = null)
    {
        // Dev mode: always shown
        if ($this->isDebug) {
            return true;
        }

        $version = Db::getInstance()->getValue('SELECT `value` FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` = "PS_INSTALL_VERSION"');
        if (!$version) {
            return false;
        }
        $installVersion = explode('.', $version);
        $currentVersion = explode('.', AppKernel::VERSION);

        // Prod mode, depends on the page
        switch ($page) {
            case 'product':
                // never show it for Product page in production mode.
                return false;
            default:
                // show only for 1.7.x
                if ($currentVersion[0] != '1' || $currentVersion[1] != '7') {
                    return false;
                }
                // show only if upgrade from older version than current one
                if ($installVersion[0] >= $currentVersion[0] || $installVersion[1] >= $currentVersion[1]) {
                    return false;
                }
        }

        return true;
    }
}
