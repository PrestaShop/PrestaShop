<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Employee;

use Employee;
use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;

/**
 * Class ContextEmployeeProvider provides context employee data.
 */
final class ContextEmployeeProvider implements ContextEmployeeProviderInterface
{
    private ?Employee $contextEmployee;

    /**
     * @param ?Employee $contextEmployee
     */
    public function __construct(?Employee $contextEmployee)
    {
        $this->contextEmployee = $contextEmployee;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuperAdmin()
    {
        return $this->contextEmployee && $this->contextEmployee->isSuperAdmin();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return (int) $this->contextEmployee?->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getLanguageId()
    {
        return (int) $this->contextEmployee?->id_lang;
    }

    /**
     * {@inheritdoc}
     */
    public function getProfileId()
    {
        return (int) $this->contextEmployee?->id_profile;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'id' => (int) $this->contextEmployee?->id,
            'profileId' => (int) $this->contextEmployee?->id_profile,
            'languageId' => (int) $this->contextEmployee?->id_lang,
            'firstname' => $this->contextEmployee?->firstname,
            'lastname' => $this->contextEmployee?->lastname,
            'email' => $this->contextEmployee?->email,
        ];
    }
}
