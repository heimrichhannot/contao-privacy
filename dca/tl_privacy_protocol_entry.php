<?php

Controller::loadDataContainer('tl_content');

$GLOBALS['TL_DCA']['tl_privacy_protocol_entry'] = [
    'config'   => [
        'dataContainer'     => 'Table',
        'ptable'            => 'tl_privacy_protocol_archive',
        'enableVersioning'  => true,
        'onload_callback'   => [
            ['HeimrichHannot\Privacy\Backend\ProtocolEntry', 'checkPermission'],
            ['HeimrichHannot\Privacy\Backend\ProtocolEntry', 'modifyDca'],
        ],
        'onsubmit_callback' => [
            ['HeimrichHannot\Haste\Dca\General', 'setDateAdded'],
        ],
        'sql'               => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list'     => [
        'label'             => [
            'fields' => ['id'],
            'format' => '%s',
        ],
        'sorting'           => [
            'mode'                  => 4,
            'fields'                => ['dateAdded DESC'],
            'headerFields'          => ['title'],
            'panelLayout'           => 'filter;sort,search,limit',
            'child_record_callback' => ['HeimrichHannot\Privacy\Backend\ProtocolEntry', 'listChildren'],
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
        ],
        'operations'        => [
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\''.$GLOBALS['TL_LANG']['MSC']['deleteConfirm'].'\'))return false;Backend.getScrollOffset()"',
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],
    'palettes' => [
        '__selector__' => [],
        'default'      => '{type_date_legend},type,dateAdded,authorType,author;'.'{user_legend},personalDataExplanation,ip,gender,academicTitle,firstname,lastname,email,member,user;'.'{interaction_legend},url,cmsScope,bundle,bundleVersion,dataContainer,description,module,moduleName,moduleType,element,elementType;'.'{code_legend},codeFile,codeLine,codeFunction,codeStacktrace;',
    ],
    'fields'   => [
        'id'                      => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'pid'                     => [
            'foreignKey' => 'tl_privacy_protocol_archive.title',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
            'relation'   => ['type' => 'belongsTo', 'load' => 'eager'],
        ],
        'tstamp'                  => [
            'label' => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['tstamp'],
            'eval'  => ['rgxp' => 'datim'],
            'sql'   => "varchar(64) NOT NULL default ''",
        ],
        // date and time
        'dateAdded'               => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['dateAdded'],
            'sorting'   => true,
            'flag'      => 7,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'timepicker' => true, 'doNotCopy' => true, 'mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        // user
        'personalDataExplanation' => [
            'inputType' => 'explanation',
            'eval'      => [
                'text'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['personalDataExplanation'], // this is a string, not an array
                'class'    => 'tl_info',
                'tl_class' => 'long',
            ],
        ],
        'ip'                      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['ip'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'tl_class' => 'w50', 'personalField' => true],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'academicTitle'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['academicTitle'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'tl_class' => 'w50', 'personalField' => true],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'gender'                  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['gender'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => ['male', 'female'],
            'reference' => &$GLOBALS['TL_LANG']['MSC']['huhPrivacy']['reference'],
            'eval'      => ['tl_class' => 'w50', 'includeBlankOption' => true, 'personalField' => true],
            'sql'       => "varchar(16) NOT NULL default ''",
        ],
        'firstname'               => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['firstname'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'tl_class' => 'w50', 'personalField' => true],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'lastname'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['lastname'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'tl_class' => 'w50', 'personalField' => true],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'email'                   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['email'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 128, 'rgxp' => 'email', 'tl_class' => 'w50', 'personalField' => true],
            'sql'       => "varchar(128) NOT NULL default ''",
        ],
        'agreement'               => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['agreement'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50', 'additionalField' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'member'                  => [
            'label'            => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['member'],
            'exclude'          => true,
            'filter'           => true,
            'inputType'        => 'select',
            'options_callback' => ['HeimrichHannot\Haste\Dca\Member', 'getMembersAsOptionsIncludingIds'],
            'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ],
        'user'                    => [
            'label'            => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['user'],
            'exclude'          => true,
            'filter'           => true,
            'inputType'        => 'select',
            'options_callback' => ['HeimrichHannot\Haste\Dca\User', 'getUsersAsOptionsIncludingIds'],
            'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ],
        // interaction
        'url'                     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['url'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "text NULL",
        ],
        'cmsScope'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['cmsScope'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => \HeimrichHannot\Privacy\Backend\ProtocolEntry::CMS_SCOPES,
            'reference' => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['reference'],
            'eval'      => ['tl_class' => 'w50', 'mandatory' => true, 'submitOnChange' => true, 'includeBlankOption' => true],
            'sql'       => "varchar(16) NOT NULL default ''",
        ],
        'bundle'                  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['bundle'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'bundleVersion'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['bundleVersion'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 32, 'tl_class' => 'w50'],
            'sql'       => "varchar(32) NOT NULL default ''",
        ],
        'dataContainer'                   => [
            'label'            => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['dataContainer'],
            'exclude'          => true,
            'filter'           => true,
            'inputType'        => 'select',
            'options_callback' => ['HeimrichHannot\Haste\Dca\General', 'getDataContainers'],
            'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true, 'chosen' => true],
            'sql'              => "varchar(64) NOT NULL default ''",
        ],
        'type'                    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['type'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => \HeimrichHannot\Privacy\Backend\ProtocolEntry::TYPES,
            'reference' => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['reference'],
            'eval'      => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'chosen' => true],
            'sql'       => "varchar(32) NOT NULL default ''",
        ],
        'description'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['description'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'textarea',
            'eval'      => ['tl_class' => 'long clr'],
            'sql'       => "text NULL",
        ],
        'additionalData'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['additionalData'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'textarea',
            'eval'      => ['tl_class' => 'long clr'],
            'sql'       => "text NULL",
        ],
        'module'                  => [
            'label'            => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['module'],
            'exclude'          => true,
            'filter'           => true,
            'inputType'        => 'select',
            'options_callback' => ['tl_content', 'getModules'],
            'wizard'           => [
                ['tl_content', 'editModule'],
            ],
            'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql'              => "varchar(64) NOT NULL default ''",
        ],
        'moduleName'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['module'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'moduleType'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['moduleType'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'element'                 => [
            'label'            => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['element'],
            'exclude'          => true,
            'filter'           => true,
            'inputType'        => 'select',
            'options_callback' => ['tl_content', 'getAlias'],
            'wizard'           => [
                ['tl_content', 'editAlias'],
            ],
            'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql'              => "varchar(64) NOT NULL default ''",
        ],
        'elementName'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['element'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'elementType'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['elementType'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'codeFile'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['codeFile'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['tl_class' => 'w50', 'codeField' => true],
            'sql'       => "text NULL",
        ],
        'codeLine'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['codeLine'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 32, 'tl_class' => 'w50', 'codeField' => true],
            'sql'       => "varchar(32) NOT NULL default ''",
        ],
        'codeFunction'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['codeFunction'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 128, 'tl_class' => 'w50', 'codeField' => true],
            'sql'       => "varchar(128) NOT NULL default ''",
        ],
        'codeStacktrace'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_protocol_entry']['codeStacktrace'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'textarea',
            'eval'      => ['class' => 'monospace', 'tl_class' => 'clr', 'rte' => 'ace', 'codeField' => true],
            'sql'       => "text NULL",
        ],
    ],
];

\HeimrichHannot\Haste\Dca\General::addAuthorFieldAndCallback('tl_privacy_protocol_entry');
\HeimrichHannot\Privacy\Util\MigrationUtil::migrateDatebaseTableField();


if (class_exists('HeimrichHannot\Exporter\ModuleExporter')) {
    $GLOBALS['TL_DCA']['tl_privacy_protocol_entry']['list']['global_operations']['export_csv'] = \HeimrichHannot\Exporter\ModuleExporter::getGlobalOperation(
        'export_csv',
        $GLOBALS['TL_LANG']['MSC']['export_csv'],
        'system/modules/exporter/assets/img/icon_export.png'
    );
    $GLOBALS['TL_DCA']['tl_privacy_protocol_entry']['list']['global_operations']['export_xls'] = \HeimrichHannot\Exporter\ModuleExporter::getGlobalOperation(
        'export_xls',
        $GLOBALS['TL_LANG']['MSC']['export_xls'],
        'system/modules/exporter/assets/img/icon_export.png'
    );
}