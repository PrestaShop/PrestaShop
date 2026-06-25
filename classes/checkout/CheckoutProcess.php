<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @return self
     */
    public function addStep(CheckoutStepInterface $step)
    {
        if ($this instanceof CheckoutProcess) {
            $step->setCheckoutProcess($this);
            $this->steps[] = $step;
        }

        return $this;
    }

    /**
     * @return AbstractCheckoutStep[]
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
     * @return array
     */
    public function getCheckoutStepsForTemplate()
    {
        return array_map(function (CheckoutStepInterface $step) {
            return [
                'identifier' => $step->getIdentifier(),
                'title' => $step->getTitle(),
                'is_reachable' => $step->isReachable(),
                'is_complete' => $step->isComplete(),
                'is_current' => $step->isCurrent(),
                'ui' => new RenderableProxy($step),
            ];
        }, $this->getSteps());
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
            'steps' => $this->getCheckoutStepsForTemplate(),
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
        /** @var AbstractCheckoutStep $step */
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
     * @throws RuntimeException if no current step is found
     */
    public function getCurrentStep()
    {
        foreach ($this->getSteps() as $step) {
            if ($step->isCurrent()) {
                return $step;
            }
        }

        throw new RuntimeException('There should be at least one current step');
    }
}
