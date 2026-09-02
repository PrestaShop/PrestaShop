<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use Symfony\Contracts\Translation\TranslatorInterface;

class CheckoutPersonalInformationStepCore extends AbstractCheckoutStep
{
    protected $template = 'checkout/_partials/steps/personal-information.tpl';
    private $loginForm;
    private $registerForm;

    private $show_login_form = false;

    /**
     * @var bool
     */
    public $logged_in;

    /**
     * @param Context $context
     * @param TranslatorInterface $translator
     * @param CustomerLoginForm $loginForm
     * @param CustomerForm $registerForm
     */
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

    public function handleRequest(array $requestParameters = [])
    {
        // personal info step is always reachable
        $this->setReachable(true);

        $this->registerForm
            ->fillFromCustomer(
                $this
                    ->getCheckoutProcess()
                    ->getCheckoutSession()
                    ->getCustomer()
            );

        if (isset($requestParameters['submitCreate'])) {
            $this->registerForm->fillWith($requestParameters);
            $hookResult = array_reduce(
                Hook::exec('actionSubmitAccountBefore', [], null, true),
                function ($carry, $item) {
                    return $carry && $item;
                },
                true
            );
            if ($hookResult && $this->registerForm->submit()) {
                $this->setNextStepAsCurrent();
                $this->setComplete(true);
            } else {
                $this->setComplete(false);
                $this->setCurrent(true);
                $this->getCheckoutProcess()->setHasErrors(true)->setNextStepReachable();
            }
        } elseif (isset($requestParameters['submitLogin'])) {
            $this->loginForm->fillWith($requestParameters);
            if ($this->loginForm->submit()) {
                $this->setNextStepAsCurrent();
                $this->setComplete(true);
            } else {
                $this->getCheckoutProcess()->setHasErrors(true);
                $this->show_login_form = true;
            }
        } elseif (array_key_exists('login', $requestParameters)) {
            $this->show_login_form = true;
            $this->setCurrent(true);
        }

        $this->logged_in = $this
            ->getCheckoutProcess()
            ->getCheckoutSession()
            ->customerHasLoggedIn();

        if ($this->logged_in && !$this->getCheckoutSession()->getCustomer()->is_guest) {
            $this->setComplete(true);
        }

        $this->setTitle(
            $this->getTranslator()->trans(
                'Personal Information',
                [],
                'Shop.Theme.Checkout'
            )
        );
    }

    public function render(array $extraParams = [])
    {
        return $this->renderTemplate(
            $this->getTemplate(),
            $extraParams,
            [
                'show_login_form' => $this->show_login_form,
                'login_form' => $this->loginForm->getProxy(),
                'register_form' => $this->registerForm->getProxy(),
                'guest_allowed' => $this->getCheckoutSession()->isGuestAllowed(),
                'empty_cart_on_logout' => !Configuration::get('PS_CART_FOLLOWING'),
            ]
        );
    }
}
