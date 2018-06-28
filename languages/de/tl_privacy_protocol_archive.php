<?php

$lang = &$GLOBALS['TL_LANG']['tl_privacy_protocol_archive'];

/**
 * Fields
 */
$lang['tstamp'][0] = 'Änderungsdatum';
$lang['title'][0]  = 'Titel';
$lang['title'][1]  = 'Geben Sie hier bitte den Titel ein.';

// config
$lang['personalFieldsExplanation']           =
    'ACHTUNG: Wählen Sie hier nur genau die Felder aus, für deren Speicherung Sie die ausdrückliche Erlaubnis des Nutzers eingeholt haben. Konsultieren Sie bei Unklarheiten unbedingt einen Anwalt! <strong>Wir als Entwickler übernehmen keinerlei Haftung!</strong>';
$lang['personalFields'][0]                   = 'Zu erfassende personenbezogene Daten';
$lang['personalFields'][1]                   = 'Wählen Sie hier die Felder aus, die erfasst werden sollen.';
$lang['titlePattern'][0]                     = 'Titelmuster';
$lang['titlePattern'][1]                     = 'Geben Sie hier ein Muster für die Titel der Protokolleinträge in der Form "%field1% %field2%" ein.';
$lang['skipIpAnonymization'][0]              = 'IP-Adressen NICHT anonymisieren';
$lang['skipIpAnonymization'][1]              = 'Wählen Sie diese Option, wenn IP-Adressen NICHT anonymisiert werden sollen.';
$lang['addCodeProtocol'][0]                  = 'Quelltextbezogene Daten erfassen';
$lang['addCodeProtocol'][1]                  = 'Wählen Sie diese Option, um quelltextbezogene Daten zu erfassen.';
$lang['codeFields'][0]                       = 'Zu erfassende quelltextbezogene Daten';
$lang['codeFields'][1]                       = 'Wählen Sie hier die Felder aus, die erfasst werden sollen.';
$lang['setReferenceFieldOnChange'][0]        = 'Setze Referenzfeld bei Opt-In-Statusänderung';
$lang['setReferenceFieldOnChange'][1]        = 'Wählen Sie diese Option, wenn bei der Veränderung des Opt-In-Status ein Feld in einer anderen Tabelle geändert werden soll.';
$lang['referenceFieldTable'][0]              = 'Tabelle des Referenzfelds';
$lang['referenceFieldTable'][1]              = 'Wählen Sie hier die Tabelle aus, die das Referenzfeld enthält.';
$lang['referenceField'][0]                   = 'Referenzfeld';
$lang['referenceField'][1]                   = 'Wählen Sie hier ein Feld aus.';
$lang['referenceFieldProtocolForeignKey'][0] = 'Protokoll-Fremdschlüssel-Feld';
$lang['referenceFieldProtocolForeignKey'][1] = 'Wählen Sie hier das Feld in "tl_privacy_protocol" aus, das zur Bestimmung des Referenzobjects genutzt werden soll.';
$lang['referenceFieldForeignKey'][0]         = 'Fremdschlüssel-Feld';
$lang['referenceFieldForeignKey'][1]         = 'Wählen Sie hier das Feld in der gewählten Tabelle aus, das zur Bestimmung des Referenzobjects genutzt werden soll.';
$lang['createInstanceOnChange'][0]           = 'Instanz für das Setzen des Referenzfeldes erzeugen (wenn nicht bereits vorhanden)';
$lang['createInstanceOnChange'][1]           = 'Wählen Sie diese Option, wenn die Model-Instanz erzeugt werden soll, sofern sie nicht schon existiert.';

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
