<?php

/**
 * Orange-localizit  is a System that transalate text into a any language.
 * Copyright (C) 2011 Orange-localizit Inc., http://www.orange-localizit.com
 *
 * Orange-localizit is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * Orange-localizit is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

/**
 * Actions class for localization module
 */
class localizationActions extends sfActions {

    private $localizationService;

    /**
     * Get Localization Service
     */
    public function getLocalizeService() {
        $this->localizationService = new LocalizationService();
        $this->localizationService->setLocalizationDao(new LocalizationDao);
        return $this->localizationService;
    }

    /**
     * This method is executed before each action
     */
    public function preExecute() {

        $userObject = $this->getUser();
        $localizationService = $this->getLocalizeService();
        $userLanguage = $localizationService->getLanguageByCode($userObject->getCulture());        

        if (!$userObject->getAttribute('user_language_id')) {
            if($userLanguage){
                $userObject->setAttribute('user_language_id',$userLanguage->getId());
            }else{
                $userObject->setAttribute('user_language_id',$localizationService->getLanguageByCode('en_US')->getId());
            }
            unset($localizationDao);
        }
    }

    /**
     *  Index Method.
     * @param sfWebRequest $request
     */
    public function executeIndex(sfWebRequest $request) {
        $localizationService = $this->getLocalizeService();
        $this->addLabelForm = new LabelForm($localizationService);
        $this->sourceLanguage = $localizationService->getLanguageByCode('en_US');
        $this->showAddLabel = false;
    }

    /**
     *  Add Label Method
     * @param sfWebRequest $request 
     */
    public function executeAddLabel(sfWebRequest $request) {
        $localizationService = $this->getLocalizeService();
        $this->addLabelForm = new LabelForm($localizationService);
        if ($request->isMethod(sfRequest::POST)) {
            $this->addLabelForm->bind($request->getParameter('addSourceForm'));

            if ($this->addLabelForm->isValid()) {
                $source = $this->addLabelForm->getValue('Label');
                $lanGroupID = $this->addLabelForm->getValue('Language_group');
                $sourceNote = $this->addLabelForm->getValue('Label_note');

                $sourcedata = new Source();
                $sourcedata->setValue($source);
                $sourcedata->setGroupId($lanGroupID);
                $sourcedata->setNote($sourceNote);

                if ($localizationService->checkSourceByGroupIdValue($lanGroupID, $source) > 0) {
                    $this->getUser()->setFlash('errorMessage', "Duplicate Source Value", true);
                } else {
                    $localizationService->addSource($sourcedata);
                    $this->getUser()->setFlash('successMessage', "Successfully Added Source", true);
                }

                $this->redirect("localization/manageLabel");
            }
        }
    }

    /**
     *  Language Label Data set method.
     * @param sfWebRequest $request 
     */
    public function executeLanguageLabelDataSet(sfWebRequest $request) {
        $localizationService = $this->getLocalizeService();

        $sourceLanguageId = $this->getUser()->getAttribute('user_language_id');
        $targetLanguageId = $request->getParameter('targetLanguageId');

        $this->sourceLanguageLabel = $this->getUser()->getCulture();
        $this->targetLanguageLabel = $localizationService->getLanguageById($targetLanguageId)->getLanguageCode();
        $this->languageLabelDataSet = $localizationService->getSourceTargetDataSet($sourceLanguageId, $targetLanguageId);
    }

    /**
     * Display Edit Button.
     */
    public function executeDisplayEditButton(sfWebRequest $request) {
        $role = $this->getUser()->getUserRole();
        $allowedLanguageList = $role->getAllowedLanguageList();
        $targetLanguageId = $request->getParameter('targetLanguageId');
        if (!in_array($targetLanguageId, $role->getAllowedLanguageList())) {
            return true;
        }
    }

    /**
     * Edit Language Label Data Method.
     * @param sfWebRequest $request 
     */
    public function executeEditLanguageLabelData(sfWebRequest $request) {
        $localizationService = $this->getLocalizeService();

        $this->sourceLanguageId = $this->getUser()->getAttribute('user_language_id');
        $this->targetLanguageId = $request->getParameter('targetLanguageId');

        $this->sourceLanguageLabel = $this->getUser()->getCulture();
        $this->targetLanguageLabel = $localizationService->getLanguageById($this->targetLanguageId)->getLanguageCode();
        $this->languageLabelDataSet = $localizationService->getSourceTargetDataSet($this->sourceLanguageId, $this->targetLanguageId);
        $this->lanagueGroupList = $localizationService->getGroupList();
    }

    /**
     *
     */
    public function executeTargetLangTextArea(sfWebRequest $request) {
        $this->setTemplate('');
    }

    /**
     * Display Language Group List.
     */
    public function executeLanguageGroupList(sfWebRequest $request) {
        if (!$this->getUser()->isAuthenticated()) {
            $this->redirect('@loginpage');
        }
        $localizationService = $this->getLocalizeService();
        $this->languageGroupList = $localizationService->getGroupList();
    }

    /**
     * Delete Language Group .
     */
    public function executeDeleteLanguageGroup(sfWebRequest $request) {
        $localizationService = $this->getLocalizeService();
        $languageGroup = $localizationService->getGroupById($request->getParameter('group_id'));
        $languageGroup->delete();
        $this->redirect('@language_group_list');
    }

    /**
     * Manage Labels
     */
    public function executeManageLabel(sfWebRequest $request) {
        if (!$this->getUser()->isAuthenticated()) {
            $this->redirect('@loginpage');
        }
        $this->searchValue = null;
        $this->languageGroupId = 0;
        $this->pageNo = $request->getParameter('pageNo', 1);
        $limit = sfConfig::get('app_items_per_page');
        $this->offset = ($this->pageNo >= 1) ? (($this->pageNo - 1) * $limit) : 0;

        $localizationService = $this->getLocalizeService();
        $this->addLabelUploadForm = new LabelUploadForm($localizationService);
        $this->addLabelForm = new LabelForm($localizationService);

        if ($request->isMethod(sfRequest::POST) && $request->getParameter('formAction') == 'uploadString') {
            $this->addLabelUploadForm->bind($request->getParameter('uploadForm'), $request->getFiles('uploadForm'));

            if ($this->addLabelUploadForm->isValid()) {

                ini_set('memory_limit', '512M');

                $lanGroupID = $this->addLabelUploadForm->getValue('Language_group');
                $targetLanguage = $this->addLabelUploadForm->getValue('Target_language');
                $fileName = $this->addLabelUploadForm->getValue('File');
                $fileType = pathinfo($fileName->getOriginalName(), PATHINFO_EXTENSION);
                if ($fileType != 'xml') {
                    $this->getUser()->setFlash("errorMessage", "Please Upload a Valid Xml File", true);
                    $this->redirect("localization/manageLabel");
                }
                $tempFilePath = $fileName->getTempName();
                $targetNote = $this->addLabelUploadForm->getValue('Target_note');


                $sourceData = new Source();
                $sourceData->setGroupId($lanGroupID);
                if ($this->addLabelUploadForm->getValue('Include_target_value')) {
                    $targetData = new Target();
                    $targetData->setLanguageId($targetLanguage);
                    $targetData->setNote($targetNote);
                    $localizationService->addSourceWithTarget($tempFilePath, $targetData, $sourceData, true);
                    $this->getUser()->setFlash("successMessage", "Successfully Loaded the Language File", true);
                    $this->redirect("localization/manageLabel");
                } else {
                    $res = $localizationService->addSourceWithTarget($tempFilePath, new Target(), $sourceData);
                    $this->getUser()->setFlash("successMessage", "Successfully Loaded the Language File", true);
                    $this->redirect("localization/manageLabel");
                }
            } else {
                echo $this->addLabelUploadForm->getErrorSchema();
            }
        }

        if ($request->isMethod(sfRequest::POST) && $request->getParameter('formAction') == 'searchString') {
            $searchForm = $request->getParameter('uploadForm');
            $this->searchValue = $request->getParameter('searchValue');
            if($this->searchValue){
                $totalRecordCount = $localizationService->getSearchResultCount($this->searchValue);
                $labelSet = $localizationService->searchSourceByValue($this->searchValue, $this->offset, $limit);
            } else {
                $form = $request->getParameter('add_label');
                $this->languageGroupId = $form['language_group_id'];

                $totalRecordCount = $localizationService->getAllSourceListCount($this->languageGroupId);
                $labelSet = $localizationService->getSourceList($this->offset, $limit, $this->languageGroupId);
            }
        } else {
            $labelSet = $localizationService->getSourceList($this->offset, $limit);
            $totalRecordCount = $localizationService->getAllSourceListCount();
        }

        $this->setPagination($this->pageNo, $limit, $totalRecordCount);
        $labelDataArray = null;

        $this->languageGroupList = $localizationService->getGroupList();
        $groupList = $localizationService->getGroupList();
        $groupArray = array();
        foreach ($groupList as $group) {
            $groupArray[$group->getId()] = $group->getName();
        }

        foreach ($labelSet as $item) {
            $labelDataArray[] = array($item->getId(), $item->getValue(), $item->getNote(), $groupArray[$item->getGroupId()]);
        }
        $this->LabelDataArray = $labelDataArray;
    }

    public function setPagination($pageNo, $limit, $totalRecordCount) {

        $pager = new SimplePager($this->className, $limit);
        $pager->setPage($pageNo);
        $pager->setNumResults($totalRecordCount);
        $pager->init();

        $this->pager = $pager;
    }

    public function executeDeleteLabelList(sfWebRequest $request) {

        $checkedid = $request->getParameter('checkedid');

        if (!empty($checkedid)) {
            $localizationService = $this->getLocalizeService();
            $localizationService->deleteSourceById($checkedid);
            $this->getUser()->setFlash('listSuccessMessage', "Successfully Deleted", true);
            $this->redirect("localization/manageLabel");
        } else {

            $this->forward("localization", "updateLabelList");
            $this->redirect("localization/manageLabel");
        }
    }

    public function executeUpdateLabelList(sfWebRequest $request) {
        $editedLabelNameArray = $request->getParameter('labelName');
        $editedLabelNoteArray = $request->getParameter('labelNote');
        $editedLabelIdArray = $request->getParameter('labelId');
        $editedLabelGroupArray = $request->getParameter('labelGroup');

        $localizationService = $this->getLocalizeService();
        $localizationService->updateSource($editedLabelIdArray, $editedLabelNameArray, $editedLabelNoteArray, $editedLabelGroupArray);
        $this->getUser()->setFlash('listSuccessMessage', "Successfully Updated", true);
        $this->forward("localization", "manageLabel");
    }

}
