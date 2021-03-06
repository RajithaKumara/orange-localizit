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
 * Description of DefineKpiService
 *
 * @author Samantha Jayasinghe
 */
class LocalizationService extends BaseService {

    private $localizationDao;

    public function getLocalizationDao() {
        return $this->localizationDao;
    }

    public function setLocalizationDao(LocalizationDao $locaizationDao) {
        $this->localizationDao = $locaizationDao;
    }

    /**
     * Add Source
     * @param $source
     * @throws ServiceException
     * @return Label
     */
    public function addSource($source) {

        $localizationDao = $this->getLocalizationDao();
        try {
            $localizationDao->addSource($source);
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Get Source by group and language
     * @param $groupId
     * @throws ServiceException
     * @return Source
     */
    public function getTargetStringByLanguageAndSourceGroupId($languageId, $groupId) {
        $localizationDao = $this->getLocalizationDao();
        try {
            return $localizationDao->getTargetStringByLanguageAndSourceGroupIdAsArray($languageId, $groupId);
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    public function getTranslateListAsArray($languageId, $groupId) {

        $sourceArray = $this->getLocalizationDao()->getSourceAsArray($groupId);
        $targetArray = $this->getLocalizationDao()->getTargetAsArray($languageId, $groupId);
        $combinedArray = array();

        foreach ($sourceArray as $sourceId => $source) {

            $combinedArray[$sourceId]['sourceValue'] = $source['sourceValue'];
            $combinedArray[$sourceId]['sourceNote'] = $source['sourceNote'];
            $combinedArray[$sourceId]['targetId'] = '';
            $combinedArray[$sourceId]['targetValue'] = '';
            $combinedArray[$sourceId]['targetNote'] = '';

            if (isset($targetArray[$sourceId])) {

                $combinedArray[$sourceId]['targetId'] = $targetArray[$sourceId]['targetId'];
                $combinedArray[$sourceId]['targetValue'] = $targetArray[$sourceId]['targetValue'];
                $combinedArray[$sourceId]['targetNote'] = $targetArray[$sourceId]['targetNote'];
            }
        }

        return $combinedArray;
    }

    /**
     * Get Source and Target list for a given group and a language
     *
     * @param integer $groupId
     * @param integer $languageId
     * @param integer $offset
     * @param integer $limit
     * @return array of data and count
     */
    public function getSourceAndTargetListAsArray($languageId, $groupId, $offset, $limit,$searchParam = array()) {
        return $this->getLocalizationDao()->getSourceAndTargetListAsArray($languageId, $groupId, $offset, $limit,$searchParam);
    }

    /**
     * Get Language List
     * @returns Language Collection
     * @return Label
     */
    public function getLanguageList() {
        $localizationDao = $this->getLocalizationDao();
        try {
            $res = $localizationDao->getLanguageList();
            return $res;
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Get Language List for user
     * @returns Language Collection
     * @return Language
     */
    public function getUserLanguageList($ids) {
        $localizationDao = $this->getLocalizationDao();
        try {
            $res = $localizationDao->getUserLanguageList($ids);
            return $res;
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Get User List by Language
     * @param int $languageId
     * @return Collection
     */
    public function getUserListByLanguage($languageId) {
        $localizationDao = $this->getLocalizationDao();
        try {
            $res = $localizationDao->getUserListByLanguage($languageId);
            return $res;
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Get Language By Code
     * @param string $languageCode
     * @return Label
     */
    public function getLanguageByCode($code) {
        $localizationDao = $this->getLocalizationDao();
        try {
            return $localizationDao->getLanguageByCode($code);
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Get Language By Id
     * @param int $languageId
     * @return Label
     */
    public function getLanguageById($id) {
        $localizationDao = $this->getLocalizationDao();
        try {
            return $localizationDao->getLanguageById($id);
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Get target set by source 
     */
    public function getSourceTargetList($targetLanguageId, $languageGroupId) {
        $localizationDao = $this->getLocalizationDao();
        $dataSet = array();
        try {
            $sourceList = $localizationDao->getDataList('Source');
            $sourceSet = $localizationDao->getTargetStringByLanguageAndSourceGroupIdAsArray($targetLanguageId, $languageGroupId);
            if ($sourceSet) {
                foreach ($sourceSet as $source) {
                    $dataRow[$source['id']]['source_id'] = $source['id'];
                    $dataRow[$source['id']]['source_value'] = $source['value'];
                    $dataRow[$source['id']]['source_note'] = $source['note'];
                    $targetSet = $source['Target'];
                    foreach ($targetSet as $target) {
                        if ($targetLanguageId == $target['languageId']) {
                            $dataRow[$source['id']]['target_id'] = $target['id'];
                            $dataRow[$source['id']]['target_language_id'] = $target['languageId'];
                            $dataRow[$source['id']]['target_value'] = $target['value'];
                        }
                    }
                    $dataSet[$source['id']] = $dataRow;
                }
            }
            return $dataSet;
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Get Target data set by source and targetLanguage
     * @param $sourceLanguageId,$targetLanguageId
     * @param string $languageCode
     * @return array
     */
    public function getSourceTargetDataSet($sourceLanguageId, $targetLanguageId) {

        $localizationDao = $this->getLocalizationDao();
        $dataSet = array();
        try {
            $labelList = $localizationDao->getDataList('Label');
            $languageLabelSet = $localizationDao->getLangStrBySrcAndTargetIds($sourceLanguageId, $targetLanguageId);

            if ($labelList) {

                foreach ($labelList as $label) {



                    $dataRow[$label->getLabelId()]['label_id'] = $label->getLabelId();
                    $dataRow[$label->getLabelId()]['label_name'] = $label->getLabelName();

                    foreach ($languageLabelSet as $languageLabel) {

                        if ($languageLabel->getLanguageLabelStringStatus() == sfConfig::get('app_status_enabled')) {

                            if ($label->getLabelId() == $languageLabel->getLabelId()) {

                                if ($sourceLanguageId == $languageLabel->getLanguageId()) {

                                    $dataRow[$label->getLabelId()]['source_language_label_string_id'] = $languageLabel->getLanguageLabelStringId();
                                    $dataRow[$label->getLabelId()]['source_language_id'] = $languageLabel->getLanguageId();
                                    $dataRow[$label->getLabelId()]['source_language_label'] = $languageLabel->getLanguageLabelString();
                                    $dataRow[$label->getLabelId()]['comment'] = $label->getLabelComment();
                                    $dataRow[$label->getLabelId()]['language_group'] = $languageLabel->getLanguageGroup()->getGroupName();
                                    $dataRow[$label->getLabelId()]['language_group_id'] = $languageLabel->getLanguageGroupId();
                                } elseif ($targetLanguageId == $languageLabel->getLanguageId()) {
                                    $dataRow[$label->getLabelId()]['target_language_label_string_id'] = $languageLabel->getLanguageLabelStringId();
                                    $dataRow[$label->getLabelId()]['target_language_id'] = $languageLabel->getLanguageId();
                                    $dataRow[$label->getLabelId()]['target_language_label'] = $languageLabel->getLanguageLabelString();
                                }
                            }
                        }
                    }
                    $dataSet[$label->getLabelId()] = $dataRow;
                }
            }
            return $dataSet;
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Create Target
     * @param Target $target
     * @throws ServiceException
     */
    public function addTarget(Target $target) {
        $localizationDao = $this->getLocalizationDao();
        try {
            $localizationDao->addTarget($target);
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Update Target
     * @param Target $target
     * @returns update row count
     * @throws ServiceException
     */
    public function updateTarget(Target $target) {
        $localizationDao = $this->getLocalizationDao();
        try {
            return $localizationDao->updateTarget($target);
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    public function deleteTarget($id) {
        $localizationDao = $this->getLocalizationDao();
        try {
            return $localizationDao->deleteTarget($id);
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Generates  Dictionary file in XML format
     * @param $sourceLanguageId
     * @param $targetLanguageId
     * @param $languageGroupId
     * @returns boolean
     * @throws ServiceException
     */
    public function generateDictionary($sourceLanguageId, $targetLanguageId, $languageGroupId) {

        try {

            $targetLanguageCode = $this->getLanguageById($targetLanguageId)->getCode();
            $targetGroup = $this->getGroupById($languageGroupId);
            $sourceLanguageCode = $this->getLanguageById($sourceLanguageId)->getCode();
            $date = date('Y-m-d\TG:i:s\Z');
            $contributorList = $this->getUserListByLanguage($targetLanguageId);
            $nameString = '';
            $nameCount = 0;
            foreach ($contributorList as $contributor) {
                $nameCount++;
                if ($nameCount > 1) {
                    $nameString .= ' , ';
                }
                $nameString .= $contributor->getFirstName() . ' ' . $contributor->getLastName();
            }
            if ($nameString != '') {
                $nameString = '<!-- Contributed By: ' . $nameString . " -->";
            }
            $xmlString = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE xliff PUBLIC "-//XLIFF//DTD XLIFF//EN" "http://www.oasis-open.org/committees/xliff/documents/xliff.dtd">
<xliff version="1.0">
<header/>
</xliff>
$nameString
XML;

            $xml = new SimpleXMLElement($xmlString);

            $languageLabelDataSet = $this->getSourceTargetList($targetLanguageId, $languageGroupId);

            $cont = 1; // loop counter
            $file = $xml->addChild('file');
            $file->addAttribute('source-language', $sourceLanguageCode);
            $file->addAttribute('target-language', $targetLanguageCode);
            $file->addAttribute('datatype', 'plaintext');
            $file->addAttribute('original', 'messages');
            $file->addAttribute('date', $date);
            $file->addAttribute('product-name', 'messages');

            $body = $file->addChild('body');
            $targetsAvailable = false;
            foreach ($languageLabelDataSet as $labelId => $languageLabelData) {

                $labelInnerData = $languageLabelData[$labelId];

                if ((!empty($labelInnerData['source_value'])) && (!empty($labelInnerData['target_value']))) {
                    $targetsAvailable = true;
                    $transunit = $body->addChild('trans-unit');
                    $transunit->addAttribute('id', $cont);
                    $transunit->addChild('source', $labelInnerData['source_value']);
                    $transunit->addChild('target', $labelInnerData['target_value']);
                    if ($labelInnerData['source_note'] != '') {
                        $transunit->addChild('note', $labelInnerData['source_note']);
                    }
                    $cont++;
                }
            }
            if (!$targetsAvailable) {
                return false;
            }
            $languageFile = sfConfig::get('sf_web_dir') . "/language_files/messages_" . $targetGroup . "." . $targetLanguageCode . ".xml";
            $fh = fopen($languageFile, 'w');
            if ($fh) {
                $formatted = $this->formatXmlString($xml->saveXML());
                $out = fwrite($fh, $formatted);
                fclose($fh);
            } else {
                throw new ServiceException("can't open file");
            }

            return TRUE;
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * formats the xml
     * @param $xml
     * @returns String
     */
    function formatXmlString($xml) {

        // add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
        $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);

        // now indent the tags
        $token = strtok($xml, "\n");
        $result = ''; // holds formatted version as it is built
        $pad = 0; // initial indent
        $matches = array(); // returns from preg_matches()
        // scan each line and adjust indent based on opening/closing tags
        while ($token !== false) :

            // test for the various tag states
            // 1. open and closing tags on same line - no change
            if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) :
                $indent = 0;
            // 2. closing tag - outdent now
            elseif (preg_match('/^<\/\w/', $token, $matches)) :
                $pad--;
            // 3. opening tag - don't pad this one, only subsequent tags
            elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
                $indent = 1;
            // 4. no indentation needed
            else :
                $indent = 0;
            endif;

            // pad the line with the required number of leading spaces
            $line = str_pad($token, strlen($token) + $pad, ' ', STR_PAD_LEFT);
            $result .= $line . "\n"; // add to the cumulative result, with linefeed
            $token = strtok("\n"); // get the next token
            $pad += $indent; // update the pad size for subsequent lines
        endwhile;

        return $result;
    }

    /**
     * Download Dictionary file
     * @param $file
     * @returns boolean
     * @throws ServiceException
     */
    public function downloadDictionary($file) {

        try {
            //file headers --
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));

            ob_clean();
            flush();
            readfile($file);

            return TRUE;
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Update Group
     * @param Group $group
     * @return update count
     * @throws Service Exception
     */
    public function updateGroup(Group $group) {
        $localizationDao = $this->getLocalizationDao();
        try {
            return $localizationDao->updateGroup($group);
        } catch (Exception $exc) {

            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Get Group by ID.
     * @param integer $id
     * @return Doctrine collection
     * @throws Service Exception
     */
    public function getGroupById($id) {
        $localizationDao = $this->getLocalizationDao();
        try {
            return $localizationDao->getGroupById($id);
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Get Group set.
     * @return Doctrine collection
     * @throws Service Exception
     */
    public function getGroupList() {
        $localizationDao = $this->getLocalizationDao();
        try {
            return $localizationDao->getDataList('Group');
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Save Group
     * @param Group $group
     * @throws Service Exception
     */
    public function saveGroup(Group $group) {
        $localizationDao = $this->getLocalizationDao();

        try {
            $localizationDao->addGroup($group);
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /* Read Label from XML
     * @returns label array
     * @throws DaoException
     */

    public function readSourceByXML($docName) {
        $doc = new DOMDocument();
        $doc->load($docName);

        if ($doc) {
            $labels = $doc->getElementsByTagName("trans-unit");
            foreach ($labels as $label) {
                $sources = $label->getElementsByTagName("source");
                $source = $sources->item(0)->nodeValue;
                $trimsource[] = trim($source);
            }
        } else {
            die("cannot load the xml");
        }

        return $trimsource;
    }

    /* Read note from XML
     * @returns source note array
     * @throws DaoException
     */

    public function readNoteByXML($docName) {
        $doc = new DOMDocument();
        $doc->load($docName);

        if ($doc) {
            $elements = $doc->getElementsByTagName("trans-unit");
            foreach ($elements as $element) {
                $notes = $element->getElementsByTagName("note");
                $note = $notes->item(0)->nodeValue;
                $trimsource[] = trim($note);
            }
        } else {
            die("cannot load the xml");
        }

        return $trimsource;
    }

    /* Read Target Language from XML
     * @returns label array
     * @throws DaoException
     */

    public function readTargetLanguageByXML($docName) {
        $doc = new DOMDocument();
        $doc->load($docName);

        if ($doc) {
            $labels = $doc->getElementsByTagName("trans-unit");
            foreach ($labels as $label) {
                $targets = $label->getElementsByTagName("target");
                $target = $targets->item(0)->nodeValue;
                $trimsource[] = trim($target);
            }
        } else {
            die("cannot load the xml");
        }

        return $trimsource;
    }

    /* save the array dif to the database with target 
     * @return saved label list
     * @throws Dao Exception
     */

    public function addSourceWithTarget($docName, Target $lstable, Source $sorcedata, $withTarget = false) {
        $localizationDao = $this->getLocalizationDao();
        try {
            $xmlarray = $this->readSourceByXML($docName);

            $unotearray = $this->readNoteByXML($docName);

            if ($withTarget) {
                $targetLanguageXml = $this->readTargetLanguageByXML($docName);
            }
            $uxmlarray = array_unique($xmlarray);

            $databasearray = $this->getSource($sorcedata->getGroupId());
            $dblabelarray = array();

            if ($withTarget) {
                $sourceTargetList = $this->getTargetStringByLanguageAndSourceGroupId($lstable->getLanguageId(), $sorcedata->getGroupId());
                $targetArray = array();
                foreach ($sourceTargetList as $source) {
                    $targets = $source['Target'];
                    $targetArray[2][] = $source['id'];
                    $targetArray[3][] = $source['value'];
                    if ($source['note']) {
                        $targetArray[4][] = $source['note'];
                    } else {
                        $targetArray[4][] = NULL;
                    }

                    if (count($targets) == 0) {
                        $targetArray[0][] = NULL;
                        $targetArray[1][] = NULL;
                    } else {
                        foreach ($targets as $target) {
                            $targetArray[0][] = $target['id'];
                            $targetArray[1][] = $target['value'];
                        }
                    }
                }
            } else {
                $sourceList = $this->getLocalizationDao()->getSourceListAsArray();
                $targetArray = array();
                foreach ($sourceList as $source) {
                    $targetArray[2][] = $source['id'];
                    $targetArray[3][] = $source['value'];
                    if ($source[note]) {
                        $targetArray[4][] = $source['note'];
                    } else {
                        $targetArray[4][] = NULL;
                    }
                }
            }

            foreach ($uxmlarray as $key => $value) {
                if (!in_array($value, $targetArray[3])) {
                    $result[0][] = $value;
                    $result[1][] = $unotearray[$key];
                    $result[2][] = $targetLanguageXml[$key];
                } else {
                    $id = array_search($value, $targetArray[3]);
                    if ($withTarget) {
                        if ($targetArray[0][$id]) {
                            if ($targetLanguageXml[$key] != $targetArray[1][$id]) {
                                $targetData = new Target();
                                $targetData->setSourceId($targetArray[2][$id]);
                                $targetData->setLanguageId($lstable->getLanguageId());
                                $targetData->setValue($targetLanguageXml[$key]);
                                $targetData->setNote($lstable->getNote());
                                $targetData->setId($targetArray[0][$id]);
                                $this->updateTarget($targetData);
                            }
                        } else {
                            $targetData = new Target();
                            $targetData->setSourceId($targetArray[2][$id]);
                            $targetData->setLanguageId($lstable->getLanguageId());
                            $targetData->setValue($targetLanguageXml[$key]);
                            $targetData->setNote($lstable->getNote());
                            $this->addTarget($targetData);
                        }
                    }
                    if ($unotearray[$key] != $targetArray[4][$id]) {
                        $idArray = array($targetArray[2][$id]);
                        $valueArray = array($targetArray[3][$id]);
                        $noteArray = array($unotearray[$key]);
                        $groupArray = array($sorcedata->getGroupId());
                        $this->updateSource($idArray, $valueArray, $noteArray, $groupArray);
                    }
                }
            }
            $usourceresults = array_unique($result[0]);
            foreach ($usourceresults as $key => $usourceresult) {
                $uresult[0][$key] = $usourceresult;
                $uresult[1][$key] = $result[1][$key];
                $uresult[2][$key] = $result[2][$key];
            }

            $j = 0;
            foreach ($uresult[0] as $key => $item) {
                $sourceData = new Source();
                $sourceData->setValue($item);
                $sourceData->setGroupId($sorcedata->getGroupId());
                $sourceData->setNote($uresult[1][$key]);
                if ($withTarget) {
                    $targetData = new Target();
                    $targetData->setSource($sourceData);
                    $targetData->setLanguageId($lstable->getLanguageId());
                    $targetData->setValue($uresult[2][$key]);
                    $targetData->setNote($lstable->getNote());
                    $sourceData->getTarget()->add($targetData);
                }
                $localizationDao->addSource($sourceData);
            }
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Get source list
     * 
     * @param $tblName
     * @param integer $limit
     * @param integer $offset
     * @returns Doctrine Collection
     */
    public function getSourceList($offset, $limit, $groupId = 0) {
        return $this->getLocalizationDao()->getAllSourceList($offset, $limit, $groupId);
    }

    /**
     * Get Source string total count
     *
     * @param $tblName
     * @returns integer count of all sources
     */
    public function getAllSourceListCount($groupId = 0) {
        return $this->getLocalizationDao()->getAllSourceListCount($groupId);
    }

    /**
     * Delete Source
     * @param $id
     * @returns delete row count
     * @throws DaoException
     */
    public function deleteSourceById($id) {

        $localizationDao = $this->getLocalizationDao();
        try {
            $res = $localizationDao->deleteSourceById($id);
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Update Source
     * @returns boolean
     * @throws DaoException
     */
    public function updateSource($id, $value, $note, $groupId) {
        $localizationDao = $this->getLocalizationDao();
        try {
            $i = 0;
            foreach ($id as $item) {
                $source = new Source();
                $source->setId($item);
                $source->setValue($value[$i]);
                $source->setNote($note[$i]);
                $source->setGroupId($groupId[$i]);
                $res = $localizationDao->updateSource($source);
                $i++;
            }
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /* get Source
     * @returns Source array
     * @throws ServiceException
     */

    public function getSource($groupid) {
        $localizationDao = $this->getLocalizationDao();
        try {
            $labelArray = $localizationDao->getSourceListByGroupId($groupid);
            return $labelArray;
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Get Source By Value
     * @param $value
     * @throws ServiceException
     * @return Label
     */
    public function getSourceByValue($value) {
        $localizationDao = $this->getLocalizationDao();
        try {
            $res = $localizationDao->getSourceByValue($value);
            return $res;
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }

    /**
     * Check Source by groupid and value
     * @param $labelName
     * @throws ServiceException
     * @return Label
     */
    public function checkSourceByGroupIdValue($groupId, $source) {
        $localizationDao = $this->getLocalizationDao();
        try {
            $res = $localizationDao->getSourceIdByByGroupIdValue($groupId, $source);
            return $res;
        } catch (Exception $exc) {
            throw new ServiceException($exc->getMessage(), $exc->getCode());
        }
    }
    
    /**
     * Search Source by Value
     * 
     * @param type $value
     * @param type $offset
     * @param type $limit
     * @return type
     */
    public function searchSourceByValue($value,$offset, $limit) {
        return $this->getLocalizationDao()->searchSourceByValue($value,$offset, $limit);
    }
    
    /**
     * Get Search results count
     * 
     * @param type $value
     * @return type
     */
    public function getSearchResultCount($value) {
        return $this->getLocalizationDao()->getSearchResultCount($value);
    }

}
