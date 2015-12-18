<?php

use Symfony\Component\Translation\TranslatorInterface;

class CheckoutPersonalInformationStepCore extends AbstractCheckoutStep
{
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

        $this->loginForm->handleRequest($requestParameters);

        if ($this->loginForm->wasSubmitted()) {
            if ($this->loginForm->hasErrors()) {
                $this->show_login_form = true;
                $this->getCheckoutProcess()->setHasErrors(true);
            } else {
                $this->step_is_complete = true;
            }
        }

        $this->registerForm
            ->fillFromCustomer(
                $this
                    ->getCheckoutProcess()
                    ->getCheckoutSession()
                    ->getCustomer()
            )
            ->handleRequest($requestParameters)
        ;

        if ($this->registerForm->wasSubmitted()) {
            if ($this->registerForm->hasErrors()) {
                $this->getCheckoutProcess()->setHasErrors(true);
            } else {
                $this->step_is_complete = true;
            }
        }

        $this->logged_in = $this
            ->getCheckoutProcess()
            ->getCheckoutSession()
            ->customerHasLoggedIn()
        ;

        $this->setTitle(
            $this->getTranslator()->trans(
                'Personal Information',
                [],
                'Checkout'
            )
        );
    }

    public function render()
    {
        return $this->renderTemplate(
            'checkout/personal-information-step.tpl', [
                'logged_in'        => $this->logged_in,
                'show_login_form'  => $this->show_login_form,
                'login_form'       => $this->loginForm->getProxy(),
                'register_form'    => $this->registerForm->getProxy()
            ]
        );
    }
}
