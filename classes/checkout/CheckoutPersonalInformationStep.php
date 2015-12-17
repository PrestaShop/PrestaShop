<?php

use Symfony\Component\Translation\TranslatorInterface;

class CheckoutPersonalInformationStepCore extends AbstractCheckoutStep
{
    private $loginForm;

    private $show_login_form = false;

    public function __construct(
        Smarty $smarty,
        TranslatorInterface $translator,
        CustomerLoginForm $loginForm
    ) {
        parent::__construct($smarty, $translator);
        $this->loginForm = $loginForm;
    }

    public function init(array $requestParameters = [])
    {
        $this->show_login_form = array_key_exists('login', $requestParameters);

        $this->loginForm->handleRequest($requestParameters);

        if ($this->loginForm->wasSubmitted()) {
            if ($this->loginForm->hasErrors()) {
                $this->show_login_form = true;
            } else {
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
                'logged_in'           => $this->logged_in,
                'show_login_form'     => $this->show_login_form,
                'rendered_login_form' => $this->loginForm->render()
            ]
        );
    }
}
