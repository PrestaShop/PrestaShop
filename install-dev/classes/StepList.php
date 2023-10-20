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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
class StepList implements IteratorAggregate
{
    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @var array
     */
    protected $steps = [];

    /**
     * @var array
     */
    private $stepNames = [];

    /**
     * @param array $stepConfig
     */
    public function __construct(array $stepConfig)
    {
        foreach ($stepConfig as $key => $config) {
            $this->stepNames[$key] = $config['name'];
            $this->steps[$key] = new Step();
            $this->steps[$key]->setName($config['name']);
            $this->steps[$key]->setDisplayName($config['displayName']);
            $this->steps[$key]->setControllerName($config['controllerClass']);
        }
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     *
     * @return StepList
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param string $stepName
     *
     * @return StepList
     */
    public function setOffsetFromStepName($stepName)
    {
        $this->offset = (int) array_search($stepName, $this->stepNames);

        return $this;
    }

    /**
     * @param string $stepName
     *
     * @return int
     */
    public function getOffsetFromStepName($stepName)
    {
        return (int) array_search($stepName, $this->stepNames);
    }

    /**
     * @return Step[]
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * @return Step
     */
    public function current()
    {
        return $this->steps[$this->offset];
    }

    /**
     * @return self
     */
    public function next()
    {
        if (array_key_exists($this->offset + 1, $this->steps)) {
            ++$this->offset;
        }

        return $this;
    }

    /**
     * @return self
     */
    public function previous()
    {
        if (array_key_exists($this->offset - 1, $this->steps)) {
            --$this->offset;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isFirstStep()
    {
        return 0 == $this->offset;
    }

    /**
     * @return bool
     */
    public function isLastStep()
    {
        return $this->offset == count($this->steps) - 1;
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->steps);
    }
}
