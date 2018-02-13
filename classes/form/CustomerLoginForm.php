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
                $this->errors[''][] = $this->translator->trans('Your account isn\'t available at this time, please contact us', [], 'Shop.Notifications.Error');
            } elseif (!$authentication || !$customer->id || $customer->is_guest) {
                $this->errors[''][] = $this->translator->trans('Authentication failed.', [], 'Shop.Notifications.Error');
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
        if (!$this->formFields) {
            $this->formFields = $this->formatter->getFormat();
        }

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
