<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


use Symfony\Component\Translation\TranslatorInterface;

class CheckoutPersonalInformationStepCore extends AbstractCheckoutStep
{
    protected $template = 'checkout/_partials/steps/personal-information.tpl';
    private $loginForm;
    private $registerForm;

    private $show_login_form = false;

    public function __construct(
        Context $context,
        TranslatorInterface $translator,
        CustomerLoginForm $loginForm,
        CustomerForm $registerForm
    ) {
        parent::__construct($context, $translator);
        $this->loginForm = $loginForm;
        $this->registerForm = $registerForm;
    }

    public function handleRequest(array $requestParameters = array())
    {
        // personal info step is always reachable
        $this->step_is_reachable = true;

        $this->registerForm
            ->fillFromCustomer(
                $this
                    ->getCheckoutProcess()
                    ->getCheckoutSession()
                    ->getCustomer()
            )
        ;

        if (isset($requestParameters['submitCreate'])) {
            $this->registerForm->fillWith($requestParameters);
            if ($this->registerForm->submit()) {
                $this->step_is_complete = true;
            } else {
                $this->step_is_complete = false;
                $this->setCurrent(true);
                $this->getCheckoutProcess()->setHasErrors(true)->setNextStepReachable();
            }
        } elseif (isset($requestParameters['submitLogin'])) {
            $this->loginForm->fillWith($requestParameters);
            if ($this->loginForm->submit()) {
                $this->step_is_complete = true;
            } else {
                $this->getCheckoutProcess()->setHasErrors(true);
                $this->show_login_form = true;
            }
        } elseif (array_key_exists('login', $requestParameters)) {
            $this->show_login_form = true;
            $this->step_is_current = true;
        }

        $this->logged_in = $this
            ->getCheckoutProcess()
            ->getCheckoutSession()
            ->customerHasLoggedIn()
        ;

        if ($this->logged_in && !$this->getCheckoutSession()->getCustomer()->is_guest) {
            $this->step_is_complete = true;
        }

        $this->setTitle(
            $this->getTranslator()->trans(
                'Personal Information',
                array(),
                'Shop.Theme.Checkout'
            )
        );
    }

    public function render(array $extraParams = array())
    {
        return $this->renderTemplate(
            $this->getTemplate(), $extraParams, array(
                'show_login_form' => $this->show_login_form,
                'login_form' => $this->loginForm->getProxy(),
                'register_form' => $this->registerForm->getProxy(),
                'guest_allowed' => $this->getCheckoutSession()->isGuestAllowed(),
                'empty_cart_on_logout' => !Configuration::get('PS_CART_FOLLOWING'),
            )
        );
    }
}
