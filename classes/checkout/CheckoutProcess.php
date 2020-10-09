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
use PrestaShop\PrestaShop\Core\Foundation\Templating\RenderableInterface;
use PrestaShop\PrestaShop\Core\Foundation\Templating\RenderableProxy;

class CheckoutProcessCore implements RenderableInterface
{
    private $smarty;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;
    /** @var array CheckoutStepInterface[] */
    private $steps = [];
    /** @var bool */
    private $has_errors;

    /** @var string */
    private $template = 'checkout/checkout-process.tpl';
    /** @var Context */
    protected $context;

    /**
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession
    ) {
        $this->context = $context;
        $this->smarty = $context->smarty;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param array $requestParameters
     *
     * @return $this
     */
    public function handleRequest(array $requestParameters = [])
    {
        foreach ($this->getSteps() as $step) {
            $step->handleRequest($requestParameters);
        }

        return $this;
    }

    /**
     * @return CheckoutSession
     */
    public function getCheckoutSession()
    {
        return $this->checkoutSession;
    }

    /**
     * @param CheckoutStepInterface $step
     *
     * @return $this
     */
    public function addStep(CheckoutStepInterface $step)
    {
        $step->setCheckoutProcess($this);
        $this->steps[] = $step;

        return $this;
    }

    /**
     * @return CheckoutStepInterface[]
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * @param array $steps
     */
    public function setSteps(array $steps)
    {
        $this->steps = $steps;
    }

    /**
     * @param string $templatePath
     *
     * @return $this
     */
    public function setTemplate($templatePath)
    {
        $this->template = $templatePath;

        return $this;
    }

    /**
     * @param array $extraParams
     *
     * @return string
     *
     * @throws SmartyException
     */
    public function render(array $extraParams = [])
    {
        $scope = $this->smarty->createData(
            $this->smarty
        );

        $params = [
            'steps' => array_map(function (CheckoutStepInterface $step) {
                return [
                    'identifier' => $step->getIdentifier(),
                    'ui' => new RenderableProxy($step),
                ];
            }, $this->getSteps()),
        ];

        $scope->assign(array_merge($extraParams, $params));

        $tpl = $this->smarty->createTemplate(
            $this->template,
            $scope
        );

        return $tpl->fetch();
    }

    /**
     * @param bool $has_errors
     *
     * @return $this
     */
    public function setHasErrors($has_errors = true)
    {
        $this->has_errors = $has_errors;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return $this->has_errors;
    }

    public function getDataToPersist()
    {
        $data = [];
        foreach ($this->getSteps() as $step) {
            $defaultStepData = [
                'step_is_reachable' => $step->isReachable(),
                'step_is_complete' => $step->isComplete(),
            ];

            $stepData = array_merge($defaultStepData, $step->getDataToPersist());

            $data[$step->getIdentifier()] = $stepData;
        }

        return $data;
    }

    public function restorePersistedData(array $data)
    {
        foreach ($this->getSteps() as $step) {
            $id = $step->getIdentifier();
            if (array_key_exists($id, $data)) {
                $stepData = $data[$id];
                $step
                    ->setReachable($stepData['step_is_reachable'])
                    ->setComplete($stepData['step_is_complete'])
                    ->restorePersistedData($stepData);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setNextStepReachable()
    {
        foreach ($this->getSteps() as $step) {
            if (!$step->isReachable()) {
                $step->setReachable(true);

                break;
            }

            if (!$step->isComplete()) {
                break;
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function markCurrentStep()
    {
        $steps = $this->getSteps();

        foreach ($steps as $step) {
            if ($step->isCurrent()) {
                // If a step marked itself as current
                // then we assume it has a good reason
                // to do so and we don't auto-advance.
                return $this;
            }
        }

        foreach ($steps as $position => $step) {
            $nextStep = ($position < count($steps) - 1) ? $steps[$position + 1] : null;

            if ($step->isReachable() && (!$step->isComplete() || ($nextStep && !$nextStep->isReachable()))) {
                $step->setCurrent(true);

                return $this;
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function invalidateAllStepsAfterCurrent()
    {
        $markAsUnreachable = false;
        foreach ($this->getSteps() as $step) {
            if ($markAsUnreachable) {
                $step->setComplete(false)->setReachable(false);
            }

            if ($step->isCurrent()) {
                $markAsUnreachable = true;
            }
        }

        return $this;
    }

    /**
     * @return CheckoutStepInterface
     *
     * @throws \RuntimeException if no current step is found
     */
    public function getCurrentStep()
    {
        foreach ($this->getSteps() as $step) {
            if ($step->isCurrent()) {
                return $step;
            }
        }

        throw new \RuntimeException('There should be at least one current step');
    }
}
