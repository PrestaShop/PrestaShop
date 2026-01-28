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

namespace PrestaShopBundle\DataCollector;

use Profiler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Throwable;

/**
 * Collects data from the legacy debug profiling (e.g. queries coming from Db class)
 */
final class LegacyCollector extends DataCollector
{
    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, ?Throwable $exception = null): void
    {
        $this->data = [
            'isProfilerEnabled' => false,
        ];
        if (_PS_DEBUG_PROFILING_) {
            // Process all profiling data
            $profiler = Profiler::getInstance();
            $profiler->processData();
            $dataVariables = $profiler->getSmartyVariables();
            if (empty($dataVariables)) {
                return;
            }
            // Clean data (else that explodes the profiler)
            foreach ($dataVariables['hooks']['perfs'] as &$dataVariableHook) {
                foreach ($dataVariableHook['modules'] as &$dataVariableHookModule) {
                    $dataVariableHookModule['params'] = [];
                }
            }
            $this->data = [
                'isProfilerEnabled' => true,
                'summary' => $dataVariables['summary'],
                'configuration' => $dataVariables['configuration'],
                'run' => $dataVariables['run'],
                'hooks' => $dataVariables['hooks'],
                'modules' => $dataVariables['modules'],
                'stopwatch' => $dataVariables['stopwatchQueries'],
                'doubles' => $dataVariables['doublesQueries'],
                'sqlTableStress' => $dataVariables['tableStress'],
                'objectModelInstances' => $dataVariables['objectmodel'],
                'includedFiles' => $dataVariables['files'],
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'ps.legacy_collector';
    }

    /**
     * {@inheritdoc}
     */
    public function reset(): void
    {
        $this->data = [];
    }

    /**
     * @return bool
     */
    public function isProfilerEnabled(): bool
    {
        return $this->data['isProfilerEnabled'];
    }

    /**
     * @return array
     */
    public function getConfiguration(): array
    {
        return $this->data['configuration'];
    }

    /**
     * @return array
     */
    public function getDoubles(): array
    {
        return $this->data['doubles'];
    }

    /**
     * @return array
     */
    public function getHooks(): array
    {
        return $this->data['hooks'];
    }

    /**
     * @return array
     */
    public function getIncludedFiles(): array
    {
        return $this->data['includedFiles'];
    }

    /**
     * @return array
     */
    public function getModules(): array
    {
        return $this->data['modules'];
    }

    /**
     * @return array
     */
    public function getObjectModelInstances(): array
    {
        return $this->data['objectModelInstances'];
    }

    /**
     * @return array
     */
    public function getRun(): array
    {
        return $this->data['run'];
    }

    /**
     * @return array
     */
    public function getSqlTableStress(): array
    {
        return $this->data['sqlTableStress'];
    }

    /**
     * @return array
     */
    public function getStopwatch(): array
    {
        return $this->data['stopwatch'];
    }

    /**
     * @return array
     */
    public function getSummary(): array
    {
        return $this->data['summary'];
    }
}
