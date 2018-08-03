<?php

$lang = &$GLOBALS['TL_LANG']['tl_module'];

/**
 * Fields
 */
$lang['privacyRestrictToJwt'][0]                       = 'Fehlermeldung ausgeben, wenn das Formular ohne JWT-Token aufgerufen wird';
$lang['privacyRestrictToJwt'][1]                       = 'Wählen Sie diese Option, wenn das Formular nicht "öffentlich", also ohne ein JWT-Token genutzt werden können soll.';
$lang['privacyAutoSubmit'][0]                          = 'Formular sofort abschicken';
$lang['privacyAutoSubmit'][1]                          = 'Wählen Sie diese Option, wenn das Formular nicht sichtbar sein und sofort abgeschickt werden soll (Wichtig: Achten Sie darauf, dass keine Pflichtfelder leer sind).';
$lang['privacyAddReferenceEntity'][0]                  = 'Referenzentität hinzufügen';
$lang['privacyAddReferenceEntity'][1]                  = 'Wählen Sie diese Option, um Auswirkungen auf Referenzentitäten zu ermöglichen (bspw. Aktualisierung der Felder, Löschen bei Opt-Out).';
$lang['privacyReferenceEntityTable'][0]                = 'Tabelle der Referenzentität';
$lang['privacyReferenceEntityTable'][1]                = 'Wählen Sie hier die Tabelle aus, in der nach einer Referenzentität gesucht werden soll.';
$lang['privacyReferenceEntityField'][0]                = 'Feld zum Auffinden der Referenzentität';
$lang['privacyReferenceEntityField'][1]                = 'Wählen Sie hier das Feld der vorab bestimmten Tabelle aus, das zum Auffinden einer Referenzentität genutzt werden soll (bspw. "email").';
$lang['privacyUpdateReferenceEntityFields'][0]         = 'Felder der Referenzentität mit Eingaben aktualisieren';
$lang['privacyUpdateReferenceEntityFields'][1]         = 'Wählen Sie diese Option, wenn die Eingaben in das Formular in die Referenzentität gespeichert werden sollen.';
$lang['privacyDeleteReferenceEntityAfterOptAction'][0] = 'Referenzentität nach dem Opt löschen';
$lang['privacyDeleteReferenceEntityAfterOptAction'][1] = 'Wählen Sie diese Option, wenn die Referenzentität nach dem Opt gelöscht werden soll.';


/**
 * Legends
 */
$lang['notification_legend'] = 'Benachrichtigung';
$lang['privacy_legend']      = 'Datenschutz';

/**
 * Reference
 */
$lang['huhPrivacy']['reference'] = [
];