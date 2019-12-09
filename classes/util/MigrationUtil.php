<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\Privacy\Util;


use Contao\Controller;
use Contao\Database;

class MigrationUtil
{
    /**
     * Migrate tl_privacy_protocol_entry.table to tl_privacy_protocol_entry.dataContainer while keeping table values.
     */
    public static function migrateDatebaseTableField()
    {
        $db = Database::getInstance();

        if ($db->tableExists('tl_privacy_protocol_entry', null, true) && $db->fieldExists('table', 'tl_privacy_protocol_entry', true)) {
            $db->execute('ALTER TABLE `tl_privacy_protocol_entry` CHANGE `table` `dataContainer` varchar(64) NOT NULL default \'\'');
            Controller::reload();
        }
    }
}