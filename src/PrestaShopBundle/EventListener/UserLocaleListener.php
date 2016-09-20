<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Translation\TranslatorInterface;

use PrestaShop\PrestaShop\Adapter\LegacyContext;

use \Language;

class UserLocaleListener
{
    private $translator;
    private $prestaShopContext;

    public function __construct(TranslatorInterface $translator, LegacyContext $context)
    {
        $this->translator = $translator;
        $this->prestaShopContext = $context->getContext();

    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (isset($this->prestaShopContext->employee) && $this->prestaShopContext->employee->isLoggedBack()) {
            $locale = $this->getLocaleFromEmployee();

            $this->translator->setLocale($locale);
        }
    }

    private function getLocaleFromEmployee()
    {
        $employee = $this->prestaShopContext->employee;
        $employeeLanguage = new \Language($employee->id_lang);

        return $employeeLanguage->locale;
    }
}
