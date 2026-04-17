<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Context;
use CustomerSession;
use DateInterval;
use DateTime;
use Db;
use EmployeeSession;
use Exception;
use PDO;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Security\Command;
use PrestaShop\PrestaShop\Core\Search\Filters\Security\Session;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class SecurityFeatureContext extends AbstractDomainFeatureContext
{
    private const SECURITY_FORM_KEY = 'security-form';

    /**
     * @Given I specify following properties for security form
     */
    public function specifyPropertiesForSecurityForm(TableNode $node): void
    {
        $data = $node->getRowsHash();

        SharedStorage::getStorage()->set(self::SECURITY_FORM_KEY, $data);
    }

    /**
     * @When I submit the security form
     */
    public function submitTheSecurityForm(): void
    {
        $data = SharedStorage::getStorage()->get(self::SECURITY_FORM_KEY);

        $request = Request::createFromGlobals();
        $request->setMethod(Request::METHOD_POST);
        $request->request->set(
            'general',
            $data
        );

        $formHandler = $this->getContainer()->get('prestashop.adapter.security.general.form_handler');
        $form = $formHandler->getForm();
        $form->handleRequest($request);

        $data = $form->getData();
        $saveErrors = $formHandler->save($data);

        if (0 !== count($saveErrors)) {
            $this->setLastException(new RuntimeException('Unable to save form: ' . print_r($saveErrors, true)));
        }

        SharedStorage::getStorage()->clear(self::SECURITY_FORM_KEY);
    }

    /**
     * @Then the security form is valid
     */
    public function securityFormIsValid(): void
    {
        $this->assertLastErrorIsNull();
    }

    /**
     * @When /^I clear outdated (customer|employee) sessions$/
     */
    public function clearOutdatedSessions(string $type): void
    {
        if ($type === 'customer') {
            $clearSessionCommand = new Command\ClearOutdatedCustomerSessionCommand();
        } else {
            $clearSessionCommand = new Command\ClearOutdatedEmployeeSessionCommand();
        }

        try {
            $this->getCommandBus()->handle($clearSessionCommand);
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then /^there is (\d+) (customer|employee) sessions? left$/
     */
    public function countSessionsLeft(int $numberOfSessions, string $type): void
    {
        if ($type === 'customer') {
            $queryBuilder = $this->getContainer()->get('prestashop.core.grid.query_builder.security.session.customer');
            $searchCriteria = Session\CustomerFilters::buildDefaults();
        } else {
            $queryBuilder = $this->getContainer()->get('prestashop.core.grid.query_builder.security.session.employee');
            $searchCriteria = Session\EmployeeFilters::buildDefaults();
        }

        $countQueryBuilder = $queryBuilder->getCountQueryBuilder($searchCriteria);
        $recordsTotal = (int) $countQueryBuilder->execute()->fetch(PDO::FETCH_COLUMN);

        Assert::assertSame(
            $numberOfSessions,
            $recordsTotal
        );
    }

    /**
     * @Then /^the token configuration should be (disabled|enabled)$/
     */
    public function tokenConfigurationIsDisabledOrEnabled(string $status): void
    {
        $token = $this->getContainer()->get('prestashop.adapter.legacy.configuration')->get('PS_SECURITY_TOKEN');
        Assert::assertSame($status === 'enabled', (bool) $token);
    }

    /**
     * @Given /^a session for (?:customer named "(.*)"|the employee) is created (\d+) hours? ago$/
     */
    public function createSession(string $name, string $hours): void
    {
        if (!empty($name)) {
            $customerId = SharedStorage::getStorage()->get($name);

            $session = new CustomerSession();
            $session->setUserId($customerId);
            $databaseTable = 'customer_session';
            $databaseIdentifier = 'id_customer_session';
        } else {
            $session = new EmployeeSession();
            $session->setUserId(Context::getContext()->employee->id);
            $databaseTable = 'employee_session';
            $databaseIdentifier = 'id_employee_session';
        }

        $session->setToken(sha1(time() . uniqid()));

        $session->add();

        $date = new DateTime();
        $date->sub(new DateInterval('PT' . $hours . 'H'));
        Db::getInstance()->execute(
            'UPDATE ' . _DB_PREFIX_ . $databaseTable . ' SET date_upd = \' ' . pSQL($date->format('Y-m-d H:i:s')) . '\'' .
            'WHERE ' . $databaseIdentifier . ' = ' . $session->id
        );
    }
}
