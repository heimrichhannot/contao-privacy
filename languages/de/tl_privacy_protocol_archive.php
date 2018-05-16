<?php

$lang = &$GLOBALS['TL_LANG']['tl_privacy_protocol_archive'];

/**
 * Fields
 */
$lang['tstamp'][0] = 'Änderungsdatum';
$lang['title'][0]  = 'Titel';
$lang['title'][1]  = 'Geben Sie hier bitte den Titel ein.';

// config
$lang['personalFieldsExplanation'] =
    'ACHTUNG: Wählen Sie hier nur genau die Felder aus, für deren Speicherung Sie die ausdrückliche Erlaubnis des Nutzers eingeholt haben. Konsultieren Sie bei Unklarheiten unbedingt einen Anwalt! <strong>Wir als Entwickler übernehmen keinerlei Haftung!</strong>';
$lang['personalFields'][0]         = 'Zu erfassende personenbezogene Daten';
$lang['personalFields'][1]         = 'Wählen Sie hier die Felder aus, die erfasst werden sollen.';
$lang['titlePattern'][0]           = 'Titelmuster';
$lang['titlePattern'][1]           = 'Geben Sie hier ein Muster für die Titel der Protokolleinträge in der Form "%field1% %field2%" ein.';
$lang['skipIpAnonymization'][0]    = 'IP-Adressen NICHT anonymisieren';
$lang['skipIpAnonymization'][1]    = 'Wählen Sie diese Option, wenn IP-Adressen NICHT anonymisiert werden sollen.';

/**
 * Reference
 */
$lang['reference'] = [];

/**
 * Legends
 */
$lang['general_legend'] = 'Titel';
$lang['config_legend']  = 'Konfiguration';

/**
 * Buttons
 */
$lang['new']    = ['Neues Protokollarchiv', 'Protokollarchiv erstellen'];
$lang['edit']   = ['Protokollarchiv bearbeiten', 'Protokollarchiv ID %s bearbeiten'];
$lang['copy']   = ['Protokollarchiv duplizieren', 'Protokollarchiv ID %s duplizieren'];
$lang['delete'] = ['Protokollarchiv löschen', 'Protokollarchiv ID %s löschen'];
$lang['toggle'] = ['Protokollarchiv veröffentlichen', 'Protokollarchiv ID %s veröffentlichen/verstecken'];
$lang['show']   = ['Protokollarchiv Details', 'Protokollarchiv-Details ID %s anzeigen'];
