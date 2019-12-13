<?php

namespace Tests\Integration\Behaviour\Features\Context\Util;

use Behat\Gherkin\Node\TableNode;
use RuntimeException;

class BehatTableNodeUtils
{
    /**
     * duplicated ir Orders pull requests
     *
     * @param TableNode $table
     *
     * @return array
     */
    public static function extractFirstRowFromProperties(TableNode $table): array
    {
        $hash = $table->getHash();
        if (count($hash) != 1) {
            throw new RuntimeException('Properties are invalid');
        }
        /** @var array $data */
        $data = $hash[0];

        return $data;
    }
}
