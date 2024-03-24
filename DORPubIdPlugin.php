<?php
namespace APP\plugins\pubIds\dor;

use APP\core\Application;
use APP\facades\Repo;
use APP\plugins\PubIdPlugin;
use PKP\components\forms\FieldText;
use PKP\components\forms\FormComponent;
use PKP\plugins\Hook;

class DORPubIdPlugin extends PubIdPlugin
{

    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);
        if (Application::isUnderMaintenance()) {
            return $success;
        }
        if ($success && $this->getEnabled($mainContextId)) {
            Hook::add('Form::config::before', [$this, 'addPublicationFormFields']);
            // Hook::add('TemplateManager::display', [$this, 'loadUrnFieldComponent']);
        }
        return $success;
    }

    /**
     * @copydoc Plugin::getDisplayName()
     */
    public function getDisplayName()
    {
        return __('plugins.pubIds.dor.displayName');
    }

    /**
     * @copydoc Plugin::getDescription()
     */
    public function getDescription()
    {
        return __('plugins.pubIds.dor.description');
    }

    public function constructPubId($pubIdPrefix, $pubIdSuffix, $contextId)
    {
        $urn = $pubIdPrefix . $pubIdSuffix;
        return $urn;
    }

    /**
     * @copydoc PKPPubIdPlugin::getPubIdType()
     */
    public function getPubIdType()
    {
        return 'other::dor';
    }

    /**
     * @copydoc PKPPubIdPlugin::getPubIdDisplayType()
     */
    public function getPubIdDisplayType()
    {
        return 'DOR';
    }

    /**
     * @copydoc PKPPubIdPlugin::getPubIdFullName()
     */
    public function getPubIdFullName()
    {
        return 'Uniform Resource Name';
    }

    /**
     * @copydoc PKPPubIdPlugin::getResolvingURL()
     */
    public function getResolvingURL($contextId, $pubId)
    {
        $resolverURL = 'https://dorl.net/dor/';
        return $resolverURL . $pubId;
    }

    /**
     * @copydoc PKPPubIdPlugin::getPubIdMetadataFile()
     */
    public function getPubIdMetadataFile()
    {
        return $this->getTemplateResource('urnSuffixEdit.tpl');
    }

    public function getPubIdAssignFile()
    {
        return $this->getTemplateResource('urnAssign.tpl');
    }

    /**
     * @copydoc PKPPubIdPlugin::instantiateSettingsForm()
     */
    public function instantiateSettingsForm($contextId)
    {
        return null;
        //return new classes\form\URNSettingsForm($this, $contextId);
    }

    /**
     * @copydoc PKPPubIdPlugin::getFormFieldNames()
     */
    public function getFormFieldNames()
    {
        return ['dorSuffix'];
    }

    /**
     * @copydoc PKPPubIdPlugin::getAssignFormFieldName()
     */
    public function getAssignFormFieldName()
    {
        return 'assignDOR';
    }

    /**
     * @copydoc PKPPubIdPlugin::getPrefixFieldName()
     */
    public function getPrefixFieldName()
    {
        return 'dorPrefix';
    }

    /**
     * @copydoc PKPPubIdPlugin::getSuffixFieldName()
     */
    public function getSuffixFieldName()
    {
        return 'dorSuffix';
    }

    /**
     * @copydoc PKPPubIdPlugin::getLinkActions()
     */
    public function getLinkActions($pubObject)
    {
        $linkActions = [];
        /*$request = Application::get()->getRequest();
        $userVars = $request->getUserVars();
        $classNameParts = explode('\\', get_class($this)); // Separate namespace info from class name
        $userVars['pubIdPlugIn'] = end($classNameParts);
        // Clear object pub id
        $linkActions['clearPubIdLinkActionURN'] = new LinkAction(
            'clearPubId',
            new RemoteActionConfirmationModal(
                $request->getSession(),
                __('plugins.pubIds.urn.editor.clearObjectsURN.confirm'),
                __('common.delete'),
                $request->url(null, null, 'clearPubId', null, $userVars),
                'modal_delete'
            ),
            __('plugins.pubIds.urn.editor.clearObjectsURN'),
            'delete',
            __('plugins.pubIds.urn.editor.clearObjectsURN')
        );

        if ($pubObject instanceof Issue) {
            // Clear issue objects pub ids
            $linkActions['clearIssueObjectsPubIdsLinkActionURN'] = new LinkAction(
                'clearObjectsPubIds',
                new RemoteActionConfirmationModal(
                    $request->getSession(),
                    __('plugins.pubIds.urn.editor.clearIssueObjectsURN.confirm'),
                    __('common.delete'),
                    $request->url(null, null, 'clearIssueObjectsPubIds', null, $userVars),
                    'modal_delete'
                ),
                __('plugins.pubIds.urn.editor.clearIssueObjectsURN'),
                'delete',
                __('plugins.pubIds.urn.editor.clearIssueObjectsURN')
            );
        }*/
        return $linkActions;
    }

    /**
     * @copydoc PKPPubIdPlugin::getSuffixPatternsFieldName()
     */
    public function getSuffixPatternsFieldNames()
    {
        return  [
            'Issue' => 'dorIssueSuffixPattern',
            'Publication' => 'dorPublicationSuffixPattern',
            'Representation' => 'dorRepresentationSuffixPattern',
        ];
    }

    /**
     * @copydoc PKPPubIdPlugin::getDAOFieldNames()
     */
    public function getDAOFieldNames()
    {
        return ['pub-id::other::dor'];
    }

    public function getSetting($contextId, $name)
    {
        if($name != "enabled") var_dump($name);
        return parent::getSetting($contextId, $name);
    }

    /**
     * @copydoc PKPPubIdPlugin::isObjectTypeEnabled()
     */
    public function isObjectTypeEnabled($pubObjectType, $contextId)
    {
        if($pubObjectType == "Publication") {
            return true;
        }
        return false;
    }

    /**
     * @copydoc PKPPubIdPlugin::isObjectTypeEnabled()
     */
    public function getNotUniqueErrorMsg()
    {
        return __('plugins.pubIds.dor.editor.urnSuffixCustomIdentifierNotUnique');
    }

    public function getDAOs()
    {
        return  [
            Repo::publication()->dao,
        ];
    }

    public function addPublicationFormFields(string $hookName, FormComponent $form): void
    {
        if ($form->id !== 'publicationIdentifiers') {
            return;
        }

        $form->addField(new FieldText('pub-id::other::dor', [
            'label' => __('plugins.pubIds.dor.displayName'),
            'description' => __('plugins.pubIds.dor.editor.urn.description'),
            'value' => $form->publication->getData('pub-id::other::dor'),
        ]));
    }

}