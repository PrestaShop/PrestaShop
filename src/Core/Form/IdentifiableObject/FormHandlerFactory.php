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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FormHandlerFactory
 */
final class FormHandlerFactory implements FormHandlerFactoryInterface
{
    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var bool
     */
    private $isDemoModeEnabled;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param TranslatorInterface $translator
     * @param FormFactoryInterface $formFactory
     * @param bool $isDemoModeEnabled
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        TranslatorInterface $translator,
        FormFactoryInterface $formFactory,
        $isDemoModeEnabled
    ) {
        $this->hookDispatcher = $hookDispatcher;
        $this->translator = $translator;
        $this->isDemoModeEnabled = $isDemoModeEnabled;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create($formType, FormDataProviderInterface $dataProvider, FormDataHandlerInterface $dataHandler)
    {
        return new FormHandler(
            $formType,
            $dataProvider,
            $dataHandler,
            $this->formFactory,
            $this->hookDispatcher,
            $this->translator,
            $this->isDemoModeEnabled
        );
    }
}
