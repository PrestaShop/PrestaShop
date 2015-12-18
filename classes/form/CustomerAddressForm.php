<?php

use Symfony\Component\Translation\TranslatorInterface;

class CustomerAddressFormCore extends AbstractForm
{
    private $context;
    private $translator;

    protected $template = 'customer/_partials/address-form.tpl';


    private $submitted;
    private $back;

    protected $errors = [

    ];

    public function __construct(
        Smarty $smarty,
        Context $context,
        TranslatorInterface $translator
    ) {
        parent::__construct($smarty);

        $this->context = $context;
        $this->translator = $translator;
    }

    public function fillWith(array $params = [])
    {
        return $this;
    }

    public function handleRequest(array $params = [])
    {
        $this->fillWith($params);

        if (isset($params['submitAddress'])) {
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
    }

    public function getTemplateVariables()
    {
        return [
            'action' => $this->action,
            'back'   => $this->back
        ];
    }
}
