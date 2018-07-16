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

/**
 * Checks that conditions for building the desired releases are met
 *
 * @todo: add unit tests
 */
class ReleaseChecker
{
    /**
     * @var VersionNumber
     */
    private $version;

    /**
     * @var ConsoleWriter
     */
    private $consoleWriter;

    /**
     * @var string
     */
    protected $lineSeparator = PHP_EOL;

    /**
     * @param string $versionNumber
     */
    public function __construct($versionNumber)
    {
        $version = new VersionNumber($versionNumber);

        $this->version = $version;
        $this->consoleWriter = new ConsoleWriter();
    }

    /**
     * @throws \Exception
     */
    public function checkRelease()
    {
        $currentBranchName = $this->getCurrentBranchName();
        $branchNameShouldBe = $this->getWhatBranchNameShouldBe($this->version);

        if (null === $currentBranchName) {
            throw new \RuntimeException('Could not find what git branch is currently checked out');
        }

        if ($currentBranchName === $branchNameShouldBe) {
            $this->consoleWriter->displayText(
                "Current branch: {$currentBranchName}. Valid for release !{$this->lineSeparator}",
                ConsoleWriter::COLOR_GREEN
            );
        } else {
            $this->consoleWriter->displayText(
                "Current branch: {$currentBranchName}. PROBLEM ! Should be {$branchNameShouldBe} instead.{$this->lineSeparator}",
                ConsoleWriter::COLOR_RED
            );
        }
    }

    /**
     * Gets name of current branch
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getCurrentBranchName()
    {
        $cmd = 'git branch';
        $output = [];
        $exitCode = null;

        exec("$cmd", $output, $exitCode);

        if ($exitCode !== 0 || (false === is_array($output))) {
            throw new \Exception("Command $cmd failed.");
        }

        foreach ($output as $line) {
            $isCurrentBranchLine = (isset($line[0]) && $line[0] === '*');

            if ($isCurrentBranchLine) {
                return trim(substr($line, 1));
            }
        }

        return null;
    }

    /**
     * @param VersionNumber $version
     *
     * @return string
     */
    private function getWhatBranchNameShouldBe(VersionNumber $version)
    {
        if ($version->isMajorVersion()) {
            return 'develop';
        }

        $branchNameShouldBe = $version->getMajorNumber() . '.' . $version->getMinorNumber() . '.x';

        return $branchNameShouldBe;
    }
}
