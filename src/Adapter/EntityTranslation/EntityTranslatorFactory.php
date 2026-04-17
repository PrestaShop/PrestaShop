<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\EntityTranslation;

use DataLangCore;
use Db;
use PrestaShop\PrestaShop\Core\Translation\EntityTranslatorInterface;
use PrestaShopBundle\Translation\TranslatorInterface;
use TabLang;

/**
 * Builds entity translators
 */
class EntityTranslatorFactory
{
    /**
     * @var Db
     */
    private $db;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var DataLangFactory
     */
    private $dataLangFactory;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->db = Db::getInstance();
        $this->dbPrefix = _DB_PREFIX_;
        $this->dataLangFactory = new DataLangFactory($this->dbPrefix, $translator);
        $this->translator = $translator;
    }

    /**
     * Builds an entity translator based on a table name
     *
     * @param string $tableName Table name (accepts with or without db prefix and _lang suffix)
     * @param string $locale IETF language tag
     *
     * @return EntityTranslatorInterface
     */
    public function buildFromTableName(string $tableName, string $locale): EntityTranslatorInterface
    {
        $dataLang = $this->dataLangFactory->buildFromTableName($tableName, $locale);

        return $this->build($dataLang);
    }

    /**
     * Builds an entity translator
     *
     * @param DataLangCore $dataLang DataLang class for this entity
     *
     * @return EntityTranslatorInterface
     */
    public function build(DataLangCore $dataLang): EntityTranslatorInterface
    {
        $selfTranslator = ($dataLang instanceof TabLang)
            ? TabTranslator::class
            : EntityTranslator::class;

        return new $selfTranslator(
            $this->db,
            $this->dbPrefix,
            $this->translator,
            $dataLang
        );
    }
}
