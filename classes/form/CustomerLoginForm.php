<?php

use Symfony\Component\Translation\TranslatorInterface;

class CustomerLoginFormCore extends AbstractForm
{
    private $context;
    private $urls;

    protected $template = 'customer/_partials/login-form.tpl';

    public function __construct(
        Smarty $smarty,
        Context $context,
        TranslatorInterface $translator,
        CustomerLoginFormatter $formatter,
        array $urls
    ) {
        parent::__construct(
            $smarty,
            $translator,
            $formatter
        );

        $this->context = $context;
        $this->translator = $translator;
        $this->formatter = $formatter;
        $this->urls = $urls;
        $this->constraintTranslator = new ValidateConstraintTranslator(
            $this->translator
        );
    }

    public function submit()
    {
        if ($this->validate()) {
            Hook::exec('actionAuthenticationBefore');

            $customer = new Customer();
            $authentication = $customer->getByEmail(
                $this->getValue('email'),
                $this->getValue('password')
            );

            if (isset($authentication->active) && !$authentication->active) {
                $this->errors[''][] = $this->translator->trans('Your account isn\'t available at this time, please contact us', [], 'Customer');
            } elseif (!$authentication || !$customer->id || $customer->is_guest) {
                $this->errors[''][] = $this->translator->trans('Authentication failed.', [], 'Customer');
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
            'action'        => $this->action,
            'urls'          => $this->urls,
            'formFields'    => array_map(
                function (FormField $field) {
                    return $field->toArray();
                },
                $this->formFields
            ),
            'errors' => $this->getErrors()
        ];
    }
}
