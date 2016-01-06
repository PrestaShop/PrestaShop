<?php

use Symfony\Component\Translation\TranslatorInterface;

class CustomerLoginFormCore extends AbstractForm
{
    private $context;
    private $translator;
    private $urls;

    protected $template = 'customer/_partials/login-form.tpl';

    private $email;
    private $password;
    private $back;

    protected $errors = [
        null        => [],
        'email'     => [],
        'password'  => []
    ];

    public function __construct(
        Smarty $smarty,
        Context $context,
        TranslatorInterface $translator,
        array $urls
    ) {
        parent::__construct($smarty);

        $this->context = $context;
        $this->translator = $translator;
        $this->urls = $urls;
    }

    public function fillWith(array $params = [])
    {
        if (isset($params['email'])) {
            $this->email = $params['email'];
        }

        if (isset($params['password'])) {
            $this->password = $params['password'];
        }

        if (isset($params['back'])) {
            $this->back = $params['back'];
        }

        return $this;
    }

    public function submit()
    {
        Hook::exec('actionAuthenticationBefore');

        if (empty($this->email)) {
            $this->errors['email'][] = $this->translator->trans('Email address is required.', [], 'Customer');
        } elseif (!Validate::isEmail($this->email)) {
            $this->errors['email'][] = $this->translator->trans('Invalid email address.', [], 'Customer');
        } elseif (empty($this->password)) {
            $this->errors['password'][] = $this->translator->trans('Password is required.', [], 'Customer');
        } elseif (!Validate::isPasswd($this->password)) {
            $this->errors['password'][] = $this->translator->trans('Invalid password.', [], 'Customer');
        } else {
            $customer = new Customer();
            $authentication = $customer->getByEmail($this->email, $this->password);
            if (isset($authentication->active) && !$authentication->active) {
                $this->errors[null][] = $this->translator->trans('Your account isn\'t available at this time, please contact us', [], 'Customer');
            } elseif (!$authentication || !$customer->id || $customer->is_guest) {
                $this->errors[null][] = $this->translator->trans('Authentication failed.', [], 'Customer');
            } else {
                $this->context->updateCustomer($customer);

                Hook::exec('actionAuthentication', ['customer' => $this->context->customer]);

                // Login information have changed, so we check if the cart rules still apply
                CartRule::autoRemoveFromCart($this->context);
                CartRule::autoAddToCart($this->context);
            }
        }

        return !$this->hasErrors();
    }

    public function getTemplateVariables()
    {
        return [
            'action' => $this->action,
            'email'  => $this->email,
            'errors' => $this->getErrors(),
            'urls'   => $this->urls,
            'back'   => $this->back
        ];
    }
}
