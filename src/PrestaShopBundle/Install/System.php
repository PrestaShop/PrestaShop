<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Install;

use ConfigurationTest;
use Requirement;
use SymfonyRequirements;

require_once __DIR__ . '/../../../var/SymfonyRequirements.php';

class System extends AbstractInstall
{
    public function checkRequiredTests()
    {
        return self::checkTests(ConfigurationTest::getDefaultTests(), 'required');
    }

    public function checkOptionalTests()
    {
        return self::checkTests(ConfigurationTest::getDefaultTestsOp(), 'optional');
    }

    // get symfony requirements
    public function checkSf2Requirements()
    {
        $symfonyRequirements = new SymfonyRequirements();
        $errors = $symfonyRequirements->getFailedRequirements();

        return $errors;
    }

    // get symfony recommendations
    public function checkSf2Recommendations()
    {
        $symfonyRequirements = new SymfonyRequirements();

        $failedRecommendations = $symfonyRequirements->getFailedRecommendations();

        return array_filter($failedRecommendations, function (Requirement $requirement) {
            if ($requirement->getTestMessage() === 'Requirements file should be up-to-date') {
                // this warning is not relevant
                return false;
            }

            return true;
        });
    }

    public function checkTests($list, $type)
    {
        $tests = ConfigurationTest::check($list);

        $success = true;
        foreach ($tests as $result) {
            $success &= ($result == 'ok') ? true : false;
        }

        return [
            'checks' => $tests,
            'success' => $success,
        ];
    }
}
