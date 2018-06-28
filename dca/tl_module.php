<?php

$dca = &$GLOBALS['TL_DCA']['tl_module'];

/**
 * Palettes
 */
$dca['palettes'][\HeimrichHannot\Privacy\Backend\Module::PROTOCOL_ENTRY_EDITOR] = '
    {title_legend},name,headline,type;
    {config_legend},formHybridDataContainer,formHybridEditable,formHybridForcePaletteRelation,formHybridAddEditableRequired,formHybridAddDisplayedSubPaletteFields,formHybridResetAfterSubmission,formHybridSuccessMessage,formHybridSkipScrollingToSuccessMessage;
    {privacy_legend},formHybridPrivacyProtocolArchive,formHybridPrivacyProtocolEntryType,formHybridPrivacyProtocolDescription,formHybridPrivacyProtocolFieldMapping,formHybridAddOptIn,privacyAddOptOut;
    {notification_legend},formHybridSendSubmissionAsNotification,formHybridSendConfirmationAsNotification;
    {redirect_legend},jumpTo;
    {template_legend},customTpl;
    {protected_legend},protected;{expert_legend},guests,cssID';

/**
 * Subpalettes
 */
$dca['palettes']['__selector__'][]                   = 'privacyAddOptOut';
$dca['palettes']['__selector__'][]                   = 'addOptOutPrivacyProtocolEntry';
$dca['palettes']['__selector__'][]                   = 'addOptOutDeletePrivacyProtocolEntry';
$dca['subpalettes']['privacyAddOptOut']              = 'privacyOptOutJumpTo,addOptOutPrivacyProtocolEntry,addOptOutDeletePrivacyProtocolEntry';
$dca['subpalettes']['addOptOutPrivacyProtocolEntry'] = 'optOutPrivacyProtocolArchive,optOutPrivacyProtocolEntryType,optOutPrivacyProtocolDescription,optOutPrivacyProtocolFieldMapping';
$dca['subpalettes']['addOptOutDeletePrivacyProtocolEntry'] = 'optOutDeletePrivacyProtocolArchive,optOutDeletePrivacyProtocolEntryType,optOutDeletePrivacyProtocolDescription,optOutDeletePrivacyProtocolFieldMapping';

/**
 * Fields
 */
$fields = [
    'privacyAddOptOut'                        => [
        'label'     => &$GLOBALS['TL_LANG']['tl_module']['privacyAddOptOut'],
        'exclude'   => true,
        'inputType' => 'checkbox',
        'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'privacyOptOutJumpTo'                     => [
        'label'      => &$GLOBALS['TL_LANG']['tl_module']['privacyOptOutJumpTo'],
        'exclude'    => true,
        'inputType'  => 'pageTree',
        'foreignKey' => 'tl_page.title',
        'eval'       => ['fieldType' => 'radio'],
        'sql'        => "int(10) unsigned NOT NULL default '0'",
        'relation'   => ['type' => 'hasOne', 'load' => 'eager']
    ],
    'addOptOutPrivacyProtocolEntry'           => $protocolManager->getSelectorFieldDca(),
    'optOutPrivacyProtocolArchive'            => $protocolManager->getArchiveFieldDca(),
    'optOutPrivacyProtocolEntryType'          => $protocolManager->getTypeFieldDca(),
    'optOutPrivacyProtocolDescription'        => $protocolManager->getDescriptionFieldDca(),
    'optOutPrivacyProtocolFieldMapping'       => $protocolManager->getFieldMappingFieldDca('formHybridDataContainer'),
    'addOptOutDeletePrivacyProtocolEntry'     => $protocolManager->getSelectorFieldDca(),
    'optOutDeletePrivacyProtocolArchive'      => $protocolManager->getArchiveFieldDca(),
    'optOutDeletePrivacyProtocolEntryType'    => $protocolManager->getTypeFieldDca(),
    'optOutDeletePrivacyProtocolDescription'  => $protocolManager->getDescriptionFieldDca(),
    'optOutDeletePrivacyProtocolFieldMapping' => $protocolManager->getFieldMappingFieldDca('formHybridDataContainer')
];

$fields['addOptOutPrivacyProtocolEntry']['label'][0]     .= ' (Opt-out)';
$fields['optOutPrivacyProtocolArchive']['label'][0]      .= ' (Opt-out)';
$fields['optOutPrivacyProtocolEntryType']['label'][0]    .= ' (Opt-out)';
$fields['optOutPrivacyProtocolDescription']['label'][0]  .= ' (Opt-out)';
$fields['optOutPrivacyProtocolFieldMapping']['label'][0] .= ' (Opt-out)';

$fields['addOptOutDeletePrivacyProtocolEntry']['label'][0]     .= ' (' . $GLOBALS['TL_LANG']['MSC']['huhPrivacy']['afterDelete'] . ')';
$fields['optOutDeletePrivacyProtocolArchive']['label'][0]      .= ' (' . $GLOBALS['TL_LANG']['MSC']['huhPrivacy']['afterDelete'] . ')';
$fields['optOutDeletePrivacyProtocolEntryType']['label'][0]    .= ' (' . $GLOBALS['TL_LANG']['MSC']['huhPrivacy']['afterDelete'] . ')';
$fields['optOutDeletePrivacyProtocolDescription']['label'][0]  .= ' (' . $GLOBALS['TL_LANG']['MSC']['huhPrivacy']['afterDelete'] . ')';
$fields['optOutDeletePrivacyProtocolFieldMapping']['label'][0] .= ' (' . $GLOBALS['TL_LANG']['MSC']['huhPrivacy']['afterDelete'] . ')';

$dca['fields'] += $fields;