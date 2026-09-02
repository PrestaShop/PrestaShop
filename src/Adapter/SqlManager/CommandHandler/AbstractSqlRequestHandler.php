<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SqlManager\CommandHandler;

use PrestaShop\PrestaShop\Adapter\SqlManager\SqlQueryValidator;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 */
abstract class AbstractSqlRequestHandler
{
    /**
     * @var SqlQueryValidator
     */
    private $sqlQueryValidator;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        SqlQueryValidator $sqlQueryValidator,
        TranslatorInterface $translator
    ) {
        $this->sqlQueryValidator = $sqlQueryValidator;
        $this->translator = $translator;
    }

    protected function assertSqlQueryIsValid(string $sql): void
    {
        $errors = $this->sqlQueryValidator->validate($sql);
        if (0 !== count($errors)) {
            $message = $this->translator->trans(
                $errors[0]['key'],
                $errors[0]['parameters'],
                $errors[0]['domain']
            );

            throw new SqlRequestConstraintException(
                $message,
                SqlRequestConstraintException::INVALID_SQL_QUERY
            );
        }
    }
}
