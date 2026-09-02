<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    public function collect(Request $request, Response $response, ?Throwable $exception = null)
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
    public function getName()
    {
        return 'ps.legacy_collector';
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
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
