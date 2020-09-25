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
use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractCheckoutStepCore implements CheckoutStepInterface
{
    private $smarty;
    private $translator;

    /**
     * @var CheckoutProcess
     */
    private $checkoutProcess;

    private $title;

    protected $step_is_reachable = false;
    protected $step_is_complete = false;
    protected $step_is_current = false;
    protected $context;

    protected $template;
    protected $unreachableStepTemplate = 'checkout/_partials/steps/unreachable.tpl';

    /**
     * @param Context $context
     * @param TranslatorInterface $translator
     */
    public function __construct(Context $context, TranslatorInterface $translator)
    {
        $this->context = $context;
        $this->smarty = $context->smarty;
        $this->translator = $translator;
    }

    public function setTemplate($templatePath)
    {
        $this->template = $templatePath;

        return $this;
    }

    public function getTemplate()
    {
        if ($this->isReachable()) {
            return $this->template;
        } else {
            return $this->unreachableStepTemplate;
        }
    }

    protected function getTranslator()
    {
        return $this->translator;
    }

    protected function renderTemplate($template, array $extraParams = [], array $params = [])
    {
        $defaultParams = [
            'title' => $this->getTitle(),
            'step_is_complete' => (int) $this->isComplete(),
            'step_is_reachable' => (int) $this->isReachable(),
            'step_is_current' => (int) $this->isCurrent(),
        ];

        $scope = $this->smarty->createData(
            $this->smarty
        );

        $scope->assign(array_merge($defaultParams, $extraParams, $params));

        $tpl = $this->smarty->createTemplate(
            $template,
            $scope
        );

        return $tpl->fetch();
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setCheckoutProcess(CheckoutProcess $checkoutProcess)
    {
        $this->checkoutProcess = $checkoutProcess;

        return $this;
    }

    public function getCheckoutProcess()
    {
        return $this->checkoutProcess;
    }

    public function getCheckoutSession()
    {
        return $this->getCheckoutProcess()->getCheckoutSession();
    }

    public function setReachable($step_is_reachable)
    {
        $this->step_is_reachable = $step_is_reachable;

        return $this;
    }

    public function isReachable()
    {
        return $this->step_is_reachable;
    }

    public function setComplete($step_is_complete)
    {
        $this->step_is_complete = $step_is_complete;

        return $this;
    }

    public function isComplete()
    {
        return $this->step_is_complete;
    }

    public function setCurrent($step_is_current)
    {
        $this->step_is_current = $step_is_current;

        return $this;
    }

    public function isCurrent()
    {
        return $this->step_is_current;
    }

    public function getIdentifier()
    {
        // SomeClassNameLikeThis => some-class-name-like-this
        return Tools::camelCaseToKebabCase(get_class($this));
    }

    public function getDataToPersist()
    {
        return [];
    }

    public function restorePersistedData(array $data)
    {
        return $this;
    }

    /**
     * Find next step and mark it as current
     */
    public function setNextStepAsCurrent()
    {
        $steps = $this->getCheckoutProcess()->getSteps();
        $next = false;
        foreach ($steps as $step) {
            if ($next === true) {
                $step->step_is_current = true;
                break;
            }

            if ($step === $this) {
                $next = true;
            }
        }
    }
}
