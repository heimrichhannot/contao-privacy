<?php

/**
 * Backend modules
 */
array_insert(
    $GLOBALS['BE_MOD'],
    1,
    [
        'privacy' => [
            'protocols' => [
                'tables' => ['tl_privacy_protocol_archive', 'tl_privacy_protocol_entry']
            ]
        ]
    ]
);

/**
 * Permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'privacy_protocols';
$GLOBALS['TL_PERMISSIONS'][] = 'privacy_protocolp';

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_privacy_protocol_archive'] = 'HeimrichHannot\Privacy\Model\ProtocolArchiveModel';
$GLOBALS['TL_MODELS']['tl_privacy_protocol_entry']   = 'HeimrichHannot\Privacy\Model\ProtocolEntryModel';