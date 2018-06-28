<?php

$dca = &$GLOBALS['TL_DCA']['tl_privacy_backend'];

$dca = [
    'config'   => [
        'dataContainer' => 'Table',
    ],
    'palettes' => [
        'default' => 'language,' . \HeimrichHannot\Privacy\Backend\ProtocolEntry::getPersonalFieldsPalette(),
    ],
    'fields'   => [
        'language' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_privacy_backend']['language'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => System::getLanguages(true),
            'eval'      => [
                'chosen'             => true,
                'rgxp'               => 'locale',
                'tl_class'           => 'w50',
                'includeBlankOption' => true,
                'mandatory'          => true,
                'skipForJwtToken'    => true
            ]
        ],
    ]
];

\HeimrichHannot\Privacy\Backend\ProtocolEntry::addPersonalPrivacyProtocolFieldsToDca('tl_privacy_backend');
unset($dca['fields']['ip']);
$dca['fields']['email']['eval']['mandatory'] = true;