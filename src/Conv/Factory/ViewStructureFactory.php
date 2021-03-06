<?php

namespace Conv\Factory;

use Conv\Structure\ViewStructure;
use Conv\Structure\ViewStructureInterface;
use Conv\Structure\ViewRawStructure;
use Symfony\Component\Yaml\Yaml;
use Conv\Util\SchemaKey;
use Conv\Util\Evaluator;
use Conv\Util\Config;
use Conv\Util\SchemaValidator;

class ViewStructureFactory
{
    /**
     * @param string $viewName
     * @param array  $spec
     * @return ViewStructureInterface
     */
    public static function fromSpec(string $viewName, array $spec): ViewStructureInterface
    {
        if ($spec[SchemaKey::TABLE_TYPE] === 'view') {
            $properties = array_diff_key($spec, array_flip(SchemaKey::VIEW_KEYS));
            $algorithm = isset($spec[SchemaKey::VIEW_ALGORITHM]) ? $spec[SchemaKey::VIEW_ALGORITHM] : null;
            return new ViewStructure(
                $viewName,
                $algorithm,
                $spec[SchemaKey::VIEW_ALIAS],
                $spec[SchemaKey::VIEW_COLUMN],
                $spec[SchemaKey::VIEW_FROM],
                $properties
            );
        } else {
            return new ViewRawStructure(
                $viewName,
                $spec[SchemaKey::VIEW_RAW_QUERY],
                array_diff_key($spec, [SchemaKey::VIEW_RAW_QUERY])
            );
        }
    }

    /**
     * @param \PDO   $pdo
     * @param string $dbName
     * @param string $viewName
     * @return ViewRawStructure
     */
    public static function fromView(\PDO $pdo, string $dbName, string $viewName): ViewRawStructure
    {
        $createQuery = $pdo->query("SHOW CREATE VIEW $viewName")->fetch()['Create View'];
        return new ViewRawStructure(
            $viewName,
            "$createQuery;",
            []
        );
    }
}
