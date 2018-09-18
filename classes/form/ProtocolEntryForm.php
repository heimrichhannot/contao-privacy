<?php

namespace HeimrichHannot\Privacy\Form;

use HeimrichHannot\FormHybrid\Form;
use HeimrichHannot\Privacy\Manager\ProtocolManager;
use HeimrichHannot\Privacy\Util\ProtocolUtil;

class ProtocolEntryForm extends Form
{
    protected $noEntity = true;
    protected $strMethod = 'POST';

    public function compile() {}

    protected function afterActivationCallback(\DataContainer $dc, $objModel, $submissionData = null)
    {
        parent::afterActivationCallback($dc, $objModel, $submissionData);

        $this->updateReferenceEntity($submissionData);
        $this->deleteReferenceEntity($submissionData);
    }

    protected function afterSubmitCallback(\DataContainer $dc)
    {
        if (!$this->objModule->formHybridAddOptIn)
        {
            $this->updateReferenceEntity();
            $this->deleteReferenceEntity();
        }
    }

    protected function updateReferenceEntity($submissionData = null)
    {
        if (!$this->objModule->privacyAddReferenceEntity || !$this->objModule->privacyUpdateReferenceEntityFields)
        {
            return;
        }

        $protocolUtil = new ProtocolUtil();

        $referenceField = $protocolUtil->getMappedPrivacyProtocolField($this->objModule->privacyReferenceEntityField, deserialize($this->objModule->formHybridPrivacyProtocolFieldMapping, true));
        $instance = null;

        if ($submissionData)
        {
            $instance = $protocolUtil->findReferenceEntity(
                $this->objModule->privacyReferenceEntityTable,
                $this->objModule->privacyReferenceEntityField,
                $submissionData->{$referenceField}
            );
        }

        if (null === $instance)
        {
            return;
        }

        $changedFields = [];

        foreach (deserialize($this->objModule->formHybridEditable, true) as $field)
        {
            if (in_array($field, ['id', 'tstamp', 'pid', 'dateAdded']))
            {
                continue;
            }

            if ($instance->{$field} != $submissionData->{$field})
            {
                $changedFields[$field] = [
                    'old' => $instance->{$field},
                    'new' => $submissionData->{$field}
                ];
            }

            $instance->{$field} = $submissionData->{$field};
        }

        if (isset($GLOBALS['TL_HOOKS']['privacy_afterUpdateReferenceEntity']) && is_array($GLOBALS['TL_HOOKS']['privacy_afterUpdateReferenceEntity'])) {
            foreach ($GLOBALS['TL_HOOKS']['privacy_afterUpdateReferenceEntity'] as $callback) {
                \System::importStatic($callback[0])->{$callback[1]}($instance, $submissionData, $changedFields, $this->objModule);
            }
        }

        $instance->save();
    }

    protected function deleteReferenceEntity($submissionData = null)
    {
        if (!$this->objModule->privacyAddReferenceEntity || !$this->objModule->privacyDeleteReferenceEntityAfterOptAction)
        {
            return;
        }

        $protocolUtil = new ProtocolUtil();

        $referenceField = $protocolUtil->getMappedPrivacyProtocolField($this->objModule->privacyReferenceEntityField, deserialize($this->objModule->formHybridPrivacyProtocolFieldMapping, true));
        $instance = null;

        if ($submissionData) {
            $instance = $protocolUtil->findReferenceEntity(
                $this->objModule->privacyReferenceEntityTable,
                $this->objModule->privacyReferenceEntityField,
                $submissionData->{$referenceField}
            );
        }

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