<?php

use Symfony\Component\Translation\TranslatorInterface;

class CustomerLoginFormCore
{
    private $smarty;
    private $context;
    private $translator;
    private $urls;

    private $templatePath = 'customer/_partials/login-form.tpl';

    private $action;

    private $email;
    private $password;
    private $back;

    private $submitted;
    private $errors = [
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
        $this->smarty = $smarty;
        $this->context = $context;
        $this->translator = $translator;
        $this->urls = $urls;
    }

    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    public function getAction()
    {
        return $this->action;
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

    public function handleRequest(array $params = [])
    {
        $this->fillWith($params);

        if (isset($params['SubmitLogin'])) {
            return $this->submit();
        } else {
            return true;
        }
    }

    public function wasSubmitted()
    {
        return $this->submitted;
    }

    public function submit()
    {
        $this->submitted = true;

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
            } elseif (!$authentication || !$customer->id) {
                $this->errors[null][] = $this->translator->trans('Authentication failed.', [], 'Customer');
            } else {
                $this->context->updateCustomer($customer);

                Hook::exec('actionAuthentication', ['customer' => $this->context->customer]);

                // Login information have changed, so we check if the cart rules still apply
                CartRule::autoRemoveFromCart($this->context);
                CartRule::autoAddToCart($this->context);
            }
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        foreach ($this->errors as $errors) {
            if (!empty($errors)) {
                return true;
            }
        }
        return false;
    }

    private function getTemplateVariables()
    {
        return [
            'action' => $this->action,
            'email'  => $this->email,
            'errors' => $this->getErrors(),
            'urls'   => $this->urls,
            'back'   => $this->back
        ];
    }

    public function render()
    {
        $scope = $this->smarty->createData(
            $this->smarty
        );

        $scope->assign($this->getTemplateVariables());

        $tpl = $this->smarty->createTemplate(
            $this->templatePath,
            $scope
        );

        return $tpl->fetch();
    }
}
