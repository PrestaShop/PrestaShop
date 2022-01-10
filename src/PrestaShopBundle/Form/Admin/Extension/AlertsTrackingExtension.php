<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
 * Appends alert messages from session flashbag to form vars.
 *
 * Usage example: when form is rendered in iframe modal, success alerts allows identifying if it was rendered after
 * successful redirect. This way we can automatically close the modal knowing that the action was successful.
 */
class AlertsTrackingExtension extends AbstractTypeExtension
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @param FlashBagInterface $flashBag
     */
    public function __construct(
        FlashBagInterface $flashBag
    ) {
        $this->flashBag = $flashBag;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        //We dont want to add alerts on every single child form, just the parent one.
        if ($form->getParent()) {
            return;
        }

        /*
         * Example: ['alerts' => ['success' => ['Success message'], 'error' => ['Invalid data']]]
         */
        $view->vars['alerts'] = $this->flashBag->peekAll();
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
