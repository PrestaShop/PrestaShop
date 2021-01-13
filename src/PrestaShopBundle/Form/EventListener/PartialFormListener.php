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

namespace PrestaShopBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;

/**
 * PartialFormListener is used to allow a form to handle partial update even if it has been prefilled with default data.
 * Usually this default data would be return via $form->getData() with data from request overridding it. In a partial
 * update we only want the data from the request.
 *
 * This listener listens to the FormEvents::PRE_SUBMIT event so that it can remove the form data right before submit. To
 * do this it needs to unlock the data in the FormBuilder, which is done in the constructor, and the HTTP method must be
 * Request::METHOD_PATCH or the Form will force absent data on submit.
 */
class PartialFormListener implements EventSubscriberInterface
{
    /**
     * @param FormBuilderInterface $builder
     */
    public function __construct(FormBuilderInterface $builder)
    {
        $builder
            ->setMethod(Request::METHOD_PATCH)
            ->setDataLocked(false)
        ;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $form->setData([]);
    }
}
