<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShopBundle\Form\Admin\Type\FormattedTextareaType;

class WebserviceKeyCore extends ObjectModel
{
    /** @var string Key */
    public $key;

    /** @var bool Webservice Account status */
    public $active = true;

    /** @var string Webservice Account description */
    public $description;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'webservice_account',
        'primary' => 'id_webservice_account',
        'fields' => [
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'key' => ['type' => self::TYPE_STRING, 'required' => true, 'size' => 32],
            'description' => ['type' => self::TYPE_STRING, 'size' => FormattedTextareaType::LIMIT_MEDIUMTEXT_UTF8_MB4],
        ],
    ];

    public function add($autodate = true, $nullValues = false)
    {
        if (WebserviceKey::keyExists($this->key)) {
            return false;
        }

        $result = parent::add($autodate = true, $nullValues = false);

        if ($result) {
            PrestaShopLogger::addLog(
                Context::getContext()->getTranslator()->trans(
                    'Webservice key created: %s',
                    [
                        $this->key,
                    ],
                    'Admin.Advparameters.Feature'
                ),
                1,
                0,
                'WebserviceKey',
                (int) $this->id,
                false,
                (int) Context::getContext()->employee->id
            );
        }

        return $result;
    }

    public static function keyExists($key)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT ' . Db::quoteIdentifier('key') . '
		FROM ' . Db::quoteIdentifier(_DB_PREFIX_ . 'webservice_account') . '
		WHERE ' . Db::quoteIdentifier('key') . ' = \'' . pSQL($key) . '\'');
    }

    public function delete()
    {
        $result = parent::delete() && ($this->deleteAssociations() !== false);

        if ($result) {
            PrestaShopLogger::addLog(
                Context::getContext()->getTranslator()->trans(
                    'Webservice key %s has been deleted',
                    [
                        $this->key,
                    ],
                    'Admin.Advparameters.Feature'
                ),
                1,
                0,
                'WebserviceKey',
                (int) $this->id,
                false,
                (int) Context::getContext()->employee->id
            );
        }

        return $result;
    }

    public function deleteAssociations()
    {
        return Db::getInstance()->delete('webservice_permission', 'id_webservice_account = ' . (int) $this->id);
    }

    /**
     * @param string $auth_key
     */
    public static function getPermissionForAccount($auth_key)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT p.*
			FROM ' . Db::quoteIdentifier(_DB_PREFIX_ . 'webservice_permission') . ' p
			LEFT JOIN ' . Db::quoteIdentifier(_DB_PREFIX_ . 'webservice_account') . ' a ON (a.id_webservice_account = p.id_webservice_account)
			WHERE a.' . Db::quoteIdentifier('key') . ' = \'' . pSQL($auth_key) . '\'
		');
        $permissions = [];
        if ($result) {
            foreach ($result as $row) {
                $permissions[$row['resource']][] = $row['method'];
            }
        }

        return $permissions;
    }

    /**
     * @param string $auth_key
     */
    public static function isKeyActive($auth_key)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT active
		FROM ' . Db::quoteIdentifier(_DB_PREFIX_ . 'webservice_account') . '
		WHERE ' . Db::quoteIdentifier('key') . ' = \'' . pSQL($auth_key) . '\'');
    }

    /**
     * @param string $auth_key
     */
    public static function getClassFromKey($auth_key)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT class_name
		FROM ' . Db::quoteIdentifier(_DB_PREFIX_ . 'webservice_account') . '
		WHERE ' . Db::quoteIdentifier('key') . ' = \'' . pSQL($auth_key) . '\'');
    }

    /**
     * @param string $auth_key
     *
     * @return int
     */
    public static function getIdFromKey(string $auth_key)
    {
        $sql = sprintf(
            'SELECT id_webservice_account FROM %s WHERE %s = \'%s\'',
            Db::quoteIdentifier(_DB_PREFIX_ . 'webservice_account'),
            Db::quoteIdentifier('key'),
            pSQL($auth_key)
        );

        return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

    /**
     * @param int $id_account
     * @param array $permissions_to_set
     *
     * @return bool
     */
    public static function setPermissionForAccount($id_account, $permissions_to_set)
    {
        $ok = true;
        $sql = 'DELETE FROM ' . Db::quoteIdentifier(_DB_PREFIX_ . 'webservice_permission') . ' WHERE id_webservice_account = ' . (int) $id_account;
        if (!Db::getInstance()->execute($sql)) {
            $ok = false;
        }
        if (is_array($permissions_to_set)) {
            $permissions = [];
            $resources = WebserviceRequest::getResources();
            $methods = ['GET', 'PUT', 'POST', 'PATCH', 'DELETE', 'HEAD'];
            foreach ($permissions_to_set as $resource_name => $resource_methods) {
                if (in_array($resource_name, array_keys($resources))) {
                    foreach (array_keys($resource_methods) as $method_name) {
                        if (in_array($method_name, $methods)) {
                            $permissions[] = [$method_name, $resource_name];
                        }
                    }
                }
            }
            $account = new WebserviceKey($id_account);
            if ($account->deleteAssociations() && $permissions) {
                // PostgreSQL's identity/serial columns reject an explicit NULL (unlike
                // MySQL's AUTO_INCREMENT, which auto-generates on NULL); the id column
                // is simply omitted from the INSERT so the sequence default applies.
                /* @phpstan-ignore-next-line */
                if (_DB_TYPE_ == 'pgsql') {
                    $sql = 'INSERT INTO ' . Db::quoteIdentifier(_DB_PREFIX_ . 'webservice_permission') . ' (resource, method, id_webservice_account) VALUES ';
                    foreach ($permissions as $permission) {
                        $sql .= '(\'' . pSQL($permission[1]) . '\', \'' . pSQL($permission[0]) . '\', ' . (int) $id_account . '), ';
                    }
                } else {
                    $sql = 'INSERT INTO ' . Db::quoteIdentifier(_DB_PREFIX_ . 'webservice_permission') . ' (id_webservice_permission, resource, method, id_webservice_account) VALUES ';
                    foreach ($permissions as $permission) {
                        $sql .= '(NULL , \'' . pSQL($permission[1]) . '\', \'' . pSQL($permission[0]) . '\', ' . (int) $id_account . '), ';
                    }
                }
                $sql = rtrim($sql, ', ');
                if (!Db::getInstance()->execute($sql)) {
                    $ok = false;
                }
            }
        }

        return $ok;
    }
}
