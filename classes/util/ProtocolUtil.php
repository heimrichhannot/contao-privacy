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
}