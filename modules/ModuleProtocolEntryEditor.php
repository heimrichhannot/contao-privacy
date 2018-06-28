<?php

namespace HeimrichHannot\Privacy\Module;

use Contao\BackendTemplate;
use Contao\Config;
use Contao\Controller;
use Contao\System;
use Firebase\JWT\JWT;
use HeimrichHannot\Haste\Model\Model;
use HeimrichHannot\Haste\Util\Url;
use HeimrichHannot\Privacy\Form\ProtocolEntryForm;
use HeimrichHannot\Privacy\Manager\ProtocolManager;
use HeimrichHannot\Privacy\Privacy;
use HeimrichHannot\Request\Request;
use HeimrichHannot\StatusMessages\StatusMessage;

class ModuleProtocolEntryEditor extends \Module
{
    protected $strTemplate = 'mod_protocol_entry_editor';

    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate           = new BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . strtoupper($GLOBALS['TL_LANG']['FMD'][$this->type][0]) . ' ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    protected function compile()
    {
        $this->doOptOut();
        $this->setDefaultValuesFromToken();

        $this->formHybridAddPrivacyProtocolEntry = true;

        $form                 = new ProtocolEntryForm($this);
        $this->Template->form = $form->generate();
    }

    protected function setDefaultValuesFromToken()
    {
        if (!($token = Request::getGet(Privacy::OPT_IN_OUT_TOKEN_PARAM)) || 'optin' !== Request::getGet(Privacy::OPT_IN_OUT_ACTION_PARAM)) {
            return;
        }

        try {
            $decoded         = JWT::decode($token, Config::get('encryptionKey'), ['HS256']);
            $decoded         = (array)$decoded;
            $decoded['data'] = (array)$decoded['data'];
        } catch (\Exception $e) {
            StatusMessage::addError($GLOBALS['TL_LANG']['MSC']['huhPrivacy']['optInTokenInvalid'], $this->id);
            return;
        }

        if (!isset($decoded['data']) || !is_array($decoded['data'])) {
            return;
        }

        $table = $this->formHybridDataContainer;

        Controller::loadDataContainer($table);
        System::loadLanguageFile($table);

        $dca = &$GLOBALS['TL_DCA'][$table];

        foreach ($decoded['data'] as $field => $value) {
            $dca['fields'][$field]['default'] = $value;
        }
    }

    protected function doOptOut()
    {
        if (!$this->privacyAddOptOut) {
            return;
        }

        $protocolManager = new ProtocolManager();

        if (!($token = Request::getGet(Privacy::OPT_IN_OUT_TOKEN_PARAM)) || 'optout' !== Request::getGet(Privacy::OPT_IN_OUT_ACTION_PARAM)) {
            return;
        }

        try {
            $decoded         = JWT::decode($token, \Config::get('encryptionKey'), ['HS256']);
            $decoded         = (array)$decoded;
            $decoded['data'] = (array)$decoded['data'];
        } catch (\Exception $e) {
            StatusMessage::addError($GLOBALS['TL_LANG']['MSC']['huhPrivacy']['optOutFailed'], $this->id);
            return;
        }

        $data = $decoded['data'];

        if (isset($decoded['referenceCondition']) && $decoded['referenceCondition']) {
            $referenceConditionArray = explode(':', $decoded['referenceCondition']);

            if (count($referenceConditionArray) === 3) {
                $table               = $referenceConditionArray[0];
                $referenceField      = $referenceConditionArray[1];
                $referenceFieldValue = $referenceConditionArray[2];

                $data['table'] = $table;

                $modelClass = Model::getClassFromTable($table);

                if (class_exists($modelClass)) {
                    $model = $modelClass::findOneBy(["$table.$referenceField=?"], [$referenceFieldValue]);

                    if ($table == 'tl_member') {
                        $data['member'] = $model->id;
                    }

                    // delete entity
                    if ($decoded['deleteInstance']) {
                        $affectedRows = $model->delete();

                        if ($affectedRows > 0 && $this->addOptOutDeletePrivacyProtocolEntry) {
                            $deleteData = $data;

                            if ($this->optOutDeletePrivacyProtocolDescription) {
                                $deleteData['description'] = $this->optOutDeletePrivacyProtocolDescription;
                            }

                            $protocolManager->addEntryFromModule(
                                $this->optOutDeletePrivacyProtocolEntryType,
                                $this->optOutDeletePrivacyProtocolArchive,
                                $deleteData,
                                $this
                            );
                        }
                    }
                }
            }
        }

        // add privacy protocol entry
        $data['description'] = $this->optOutPrivacyProtocolDescription;

        $protocolManager->addEntryFromModule(
            $this->optOutPrivacyProtocolEntryType,
            $this->optOutPrivacyProtocolArchive,
            $data,
            $this
        );

        if ($this->privacyOptOutJumpTo) {
            Controller::redirect(Url::getJumpToPageUrl($this->privacyOptOutJumpTo));
        } else {
            StatusMessage::addSuccess($GLOBALS['TL_LANG']['MSC']['huhPrivacy']['optOutSuccessful'], $this->id);
        }
    }
}