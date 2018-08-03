<?php

namespace HeimrichHannot\Privacy\Form;

use HeimrichHannot\FormHybrid\Form;
use HeimrichHannot\FormHybrid\FormHybrid;
use HeimrichHannot\Privacy\Manager\ProtocolManager;
use HeimrichHannot\Privacy\Util\ProtocolUtil;

class ProtocolEntryForm extends Form
{
    protected $noEntity = true;
    protected $strMethod = 'POST';

    public function compile() {}

    protected function afterActivationCallback(\DataContainer $dc, $objModel)
    {
        parent::afterActivationCallback($dc, $objModel);

        $this->updateReferenceEntity();
        $this->deleteReferenceEntity();
    }

    protected function afterSubmitCallback(\DataContainer $dc)
    {
        if (!$this->objModule->formHybridAddOptIn)
        {
            $this->updateReferenceEntity();
            $this->deleteReferenceEntity();
        }
    }

    protected function updateReferenceEntity()
    {
        if (!$this->objModule->privacyAddReferenceEntity || !$this->objModule->privacyUpdateReferenceEntityFields)
        {
            return;
        }

        $session = \Session::getInstance();

        $decoded = $session->get('PRIVACY_DATA_' . $this->objModule->id);

        if (!is_array($decoded))
        {
            return;
        }

        $protocolUtil = new ProtocolUtil();

        $instance = $protocolUtil->findReferenceEntity(
            $this->objModule->privacyReferenceEntityTable,
            $this->objModule->privacyReferenceEntityField,
            $decoded['referenceFieldValue']
        );

        if (null === $instance)
        {
            return;
        }

        $submission = $this->getSubmission()->row();

        foreach (deserialize($this->objModule->formHybridEditable, true) as $field)
        {
            if (in_array($field, ['id', 'tstamp', 'pid', 'dateAdded']))
            {
                continue;
            }

            $instance->{$field} = $submission[$field];
        }

        $instance->save();
    }

    protected function deleteReferenceEntity()
    {
        if (!$this->objModule->privacyAddReferenceEntity || !$this->objModule->privacyDeleteReferenceEntityAfterOptAction)
        {
            return;
        }

        $session = \Session::getInstance();

        $decoded = $session->get('PRIVACY_DATA_' . $this->objModule->id);

        if (!is_array($decoded))
        {
            return;
        }

        $protocolUtil = new ProtocolUtil();

        $instance = $protocolUtil->findReferenceEntity(
            $this->objModule->privacyReferenceEntityTable,
            $this->objModule->privacyReferenceEntityField,
            $decoded['referenceFieldValue']
        );

        if (null === $instance)
        {
            return;
        }

        $data = $instance->row();

        $data['table'] = $this->objModule->privacyReferenceEntityTable;

        if ($this->objModule->privacyReferenceEntityTable == 'tl_member') {
            $data['member'] = $instance->id;
        }

        // delete entity
        $affectedRows = $instance->delete();

        if ($affectedRows > 0 && $this->objModule->addOptOutDeletePrivacyProtocolEntry) {
            if ($this->objModule->optOutDeletePrivacyProtocolDescription) {
                $data['description'] = $this->objModule->optOutDeletePrivacyProtocolDescription;
            }

            $protocolManager = new ProtocolManager();

            $protocolManager->addEntryFromModule(
                $this->objModule->optOutDeletePrivacyProtocolEntryType,
                $this->objModule->optOutDeletePrivacyProtocolArchive,
                $data,
                $this->objModule
            );
        }
    }
}