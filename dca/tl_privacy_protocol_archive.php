<?php

$GLOBALS['TL_DCA']['tl_privacy_protocol_archive'] = [
    'config' => [
        'dataContainer'     => 'Table',
        'ctable'            => ['tl_privacy_protocol_entry'],
        'switchToEdit'                => true,
        'enableVersioning'  => true,
        'onload_callback' => [
            ['HeimrichHannot\Privacy\Backend\ProtocolArchive', 'checkPermission'],
        ],
        'onsubmit_callback' => [
            ['HeimrichHannot\Haste\Dca\General', 'setDateAdded'],
        ],
        'sql' => [
            'keys' => [
                'id' => 'primary'
            ]
        ]
    ],
    'list' => [
        'label' => [
            'fields' => ['title'],
            'format' => '%s'
        ],
        'sorting'           => [
            'mode'                  => 1,
            'fields'                => ['title'],
            'headerFields'          => ['title'],
            'panelLayout'           => 'filter;search,limit'
        ],
        'global_operations' => [
            'all'    => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ],
        ],
        'operations' => [
            'edit' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_privacy_protocol_archive']['edit'],
                'href'                => 'table=tl_privacy_protocol_entry',
                'icon'                => 'edit.gif'
            ],
            'editheader' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_privacy_protocol_archive']['editheader'],
                'href'                => 'act=edit',
                'icon'                => 'header.gif',
                'button_callback'     => ['HeimrichHannot\Privacy\Backend\ProtocolArchive', 'editHeader']
            ],
            'copy' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_privacy_protocol_archive']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif',
                'button_callback'     => ['HeimrichHannot\Privacy\Backend\ProtocolArchive', 'copyArchive']
            ],
            'delete' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_privacy_protocol_archive']['copy'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
                'button_callback'     => ['HeimrichHannot\Privacy\Backend\ProtocolArchive', 'deleteArchive']
            ],
            'toggle' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_privacy_protocol_archive']['toggle'],
                'href'                => 'act=toggle',
                'icon'                => 'toggle.gif'
            ],
            'show' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_privacy_protocol_archive']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            ],
        ]
    ],
    'palettes' => [
        '__selector__' => [],
        'default' => '{general_legend},title;'
    ],
    'fields'   => [
        'id' => [
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ],
        'tstamp' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_privacy_protocol_archive']['tstamp'],
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ],
        'dateAdded' => [
            'label'                   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
            'sorting'                 => true,
            'flag'                    => 6,
            'eval'                    => ['rgxp'=>'datim', 'doNotCopy' => true],
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ],
        'title' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_privacy_protocol_archive']['title'],
            'exclude'                 => true,
            'search'                  => true,
            'sorting'                 => true,
            'flag'                    => 1,
            'inputType'               => 'text',
            'eval'                    => ['mandatory' => true, 'tl_class'=>'w50'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'fields' => [
            'label'            => &$GLOBALS['TL_LANG']['tl_privacy_protocol_archive']['fields'],
            'exclude'          => true,
            'inputType'        => 'checkboxWizard',
            'options_callback' => ['HeimrichHannot\Privacy\Backend\PrivacyEntry', 'getFieldsAsOptions'],
            'eval'             => ['multiple' => true, 'tl_class' => 'w50 clr'],
            'sql'              => "blob NULL",
        ],
        'titlePattern'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['titlePattern'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 128, 'tl_class' => 'w50'],
            'sql'       => "varchar(128) NOT NULL default ''",
        ],
    ]
];