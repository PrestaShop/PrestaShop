<?php

use Symfony\Component\Translation\TranslatorInterface;

class CheckoutPersonalInformationStepCore extends AbstractCheckoutStep
{
    protected $template = 'checkout/personal-information-step.tpl';
    private $loginForm;
    private $registerForm;

    private $show_login_form = false;

    public function __construct(
        Smarty $smarty,
        TranslatorInterface $translator,
        CustomerLoginForm $loginForm,
        CustomerRegisterForm $registerForm
    ) {
        parent::__construct($smarty, $translator);
        $this->loginForm = $loginForm;
        $this->registerForm = $registerForm;
    }

    public function handleRequest(array $requestParameters = [])
    {
        // personal info step is always reachable
        $this->step_is_reachable = true;

        $this->show_login_form = array_key_exists('login', $requestParameters);
        if ($this->show_login_form) {
            $this->step_is_current = true;
        }

        $this->loginForm->fillWith($requestParameters);

        $this->registerForm
            ->fillFromCustomer(
                $this
                    ->getCheckoutProcess()
                    ->getCheckoutSession()
                    ->getCustomer()
            )
            ->fillWith($requestParameters)
        ;

        if (isset($requestParameters['submitCreate'])) {
            if ($this->registerForm->submit()) {
                $this->step_is_complete = true;
            } else {
                $this->getCheckoutProcess()->setHasErrors(true);
            }
        } elseif (isset($requestParameters['submitLogin'])) {
            if ($this->loginForm->submit()) {
                $this->step_is_complete = true;
            } else {
                $this->getCheckoutProcess()->setHasErrors(true);
            }
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
                [],
                'Checkout'
            )
        );
    }

    public function render(array $extraParams = [])
    {
        return $this->renderTemplate(
            $this->template, $extraParams, [
                'logged_in'        => $this->logged_in,
                'show_login_form'  => $this->show_login_form,
                'login_form'       => $this->loginForm->getProxy(),
                'register_form'    => $this->registerForm->getProxy()
            ]
        );
    }
}
