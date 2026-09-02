<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Appends alert messages from session flashbag to form vars.
 *
 * Usage example: when form is rendered in iframe modal, success alerts allows identifying if it was rendered after
 * successful redirect. This way we can automatically close the modal knowing that the action was successful.
 */
class AlertsTrackingExtension extends AbstractTypeExtension
{
    /**
     * @param RequestStack $requestStack
     */
    public function __construct(
        private readonly RequestStack $requestStack
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // We dont want to add alerts on every single child form, just the parent one.
        if ($form->getParent()) {
            return;
        }

        /** @var Session $session */
        $session = $this->requestStack->getSession();

        /*
         * Example: ['alerts' => ['success' => ['Success message'], 'error' => ['Invalid data']]]
         */
        $view->vars['alerts'] = $session->getFlashBag()->peekAll();
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
