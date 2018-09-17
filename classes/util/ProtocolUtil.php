<?php

namespace HeimrichHannot\Privacy\Util;

use Contao\Model;

class ProtocolUtil {
    public function findReferenceEntity($table, $field, $fieldValue)
    {
        $modelClass = Model::getClassFromTable($table);

        if (class_exists($modelClass)) {
            return $modelClass::findOneBy(["$field=?"], [$fieldValue]);
        }

        return false;
    }

    public function getMappedPrivacyProtocolFieldValues($data, $mapping)
    {
        foreach ($mapping as $mappingData)
        {
            $data[$mappingData['protocolField']] = $data[$mappingData['entityField']];
        }

        return $data;
    }

    public function getMappedPrivacyProtocolField($entityField, $mapping)
    {
        foreach ($mapping as $mappingData)
        {
            if ($mappingData['entityField'] === $entityField)
            {
                return $mappingData['protocolField'];
            }
        }

        return $entityField;
    }
}