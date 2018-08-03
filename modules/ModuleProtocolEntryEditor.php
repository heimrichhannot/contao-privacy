<?php

namespace HeimrichHannot\Privacy\Module;

use Contao\BackendTemplate;
use Contao\Config;
use Contao\Controller;
use Contao\System;
use Firebase\JWT\JWT;
use HeimrichHannot\FormHybrid\FormHelper;
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
        $decoded = $this->getDataFromJwtToken();

        if ($this->privacyAutoSubmit)
        {
            $formId = FormHelper::getFormId($this->formHybridDataContainer, $this->id);

            if ($this->useCustomFormId)
            {
                $formId = $this->customFormId;
            }

            Request::setPost('FORM_SUBMIT', $formId);
        }

        if (is_array($decoded))
        {
            $this->setDefaultValuesFromToken($decoded);
            $this->storeJwtDataToSession($decoded);
        }

        $this->formHybridAddPrivacyProtocolEntry = true;

        $form                 = new ProtocolEntryForm($this);
        $this->Template->form = $form->generate();
    }

    protected function getDataFromJwtToken()
    {
        if (!($token = Request::getGet(Privacy::OPT_IN_OUT_TOKEN_PARAM)) || !Request::getGet(Privacy::OPT_ACTION_PARAM)) {
            if ($this->privacyRestrictToJwt) {
                StatusMessage::addError($GLOBALS['TL_LANG']['MSC']['huhPrivacy']['messageNoJwtToken'], $this->id);
            }

            return false;
        }

        try {
            $decoded         = JWT::decode($token, Config::get('encryptionKey'), ['HS256']);
            $decoded         = (array)$decoded;
            $decoded['data'] = (array)$decoded['data'];
        } catch (\Exception $e) {
            StatusMessage::addError($GLOBALS['TL_LANG']['MSC']['huhPrivacy']['optInTokenInvalid'], $this->id);
            return false;
        }

        return $decoded;
    }

    protected function setDefaultValuesFromToken($decoded)
    {
        if (!isset($decoded['data']) || !is_array($decoded['data'])) {
            return;
        }

        $table = $this->formHybridDataContainer;

        Controller::loadDataContainer($table);
        System::loadLanguageFile($table);

        $dca = &$GLOBALS['TL_DCA'][$table];

        foreach ($decoded['data'] as $field => $value) {
            if ($this->privacyAutoSubmit)
            {
                Request::setPost($field, $value);
            }
            else{
                $dca['fields'][$field]['default'] = $value;
            }
        }
    }

    protected function storeJwtDataToSession($decoded)
    {
        $session = \Session::getInstance();

        $session->set('PRIVACY_DATA_' . $this->id, $decoded);
    }
}