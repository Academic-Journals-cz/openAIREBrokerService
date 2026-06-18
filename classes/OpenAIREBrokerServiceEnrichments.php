<?php

/**
 * @file plugins/generic/openAIREBrokerService/classes/OpenAIREBrokerServiceEnrichments.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class OpenAIREBrokerServiceEnrichments
 * @ingroup plugins_generic_openAIREBrokerService
 *
 * @brief Supporting class for handling enrichments in two different grids
 */

namespace APP\plugins\generic\openAIREBrokerService\classes;

use APP\core\Application;

class OpenAIREBrokerServiceEnrichments {

    protected $_brokerServiceApi = "http://api.openaire.eu/broker/scroll/notifications/bySubscriptionId/";
    protected $_brokerServiceApiNextpage = "http://api.openaire.eu/broker/scroll/notifications/";
    protected $_enrichs = array('ENRICH/MORE/OPENACCESS_VERSION' => 'enrich_more_openaccess_version',
        'ENRICH/MORE/PID' => 'enrich_more_pid',
        'ENRICH/MISSING/AUTHOR/ORCID' => 'enrich_missing_author_orcid',
        'ENRICH/MISSING/PID' => 'enrich_missing_pid',
        'ENRICH/MISSING/ABSTRACT' => 'enrich_missing_abstract',
        'ENRICH/MISSING/SUBJECT/DDC' => 'enrich_missing_subject_ddc',
        'ENRICH/MORE/SUBJECT/DDC' => 'enrich_more_subject_ddc',
        'ENRICH/MISSING/SUBJECT/JEL' => 'enrich_missing_subject_jel',
        'ENRICH/MORE/SUBJECT/JEL' => 'enrich_more_subject_jel',
        'ENRICH/MISSING/PUBLICATION_DATE' => 'enrich_missing_publication_date',
        'ENRICH/MISSING/OPENACCESS_VERSION' => 'enrich_missing_openaccess_version',
        'ENRICH/MISSING/SUBJECT/ACM' => 'enrich_missing_subject_acm',
        'ENRICH/MORE/SUBJECT/ACM' => 'enrich_more_subject_acm',
        'ENRICH/MISSING/PROJECT' => 'enrich_missing_project',
        'ENRICH/MISSING/SUBJECT/MESHEUROPMC' => 'enrich_missing_subject_mesheuropmc',
        'ENRICH/MORE/SUBJECT/MESHEUROPMC' => 'enrich_more_subject_mesheuropmc',
        'ENRICH/MISSING/SUBJECT/ARXIV' => 'enrich_missing_subject_arxiv',
        'ENRICH/MORE/SUBJECT/ARXIV' => 'enrich_more_subject_arxiv');
    protected $_contextErichments = array();

    /**
     * Insert Article enrichments to Publication part of workflow
     */
    function articleEnrichments($submissionId) {
        $subscriptions = $this->getSubscriptions();
        $articleMessages = array();
        foreach ($subscriptions as $topicKey => $subscription) {
            $articleMessages = array_merge($articleMessages, $this->getArticleMessages($subscription, $submissionId, $topicKey));
        }
        return $articleMessages;
    }

    /**
     * Context enrichments
     */
    function contextEnrichments() {
        $subscriptions = $this->getSubscriptions();
        $contextEnrichments = array();
        foreach ($subscriptions as $topicKey => $subscription) {
            $contextEnrichments = $this->arrayMergeByKey($contextEnrichments, $this->getArticleMessages($subscription, null, $topicKey));
        }
        arsort($contextEnrichments);
        return $contextEnrichments;
    }

    function arrayMergeByKey($array1, $array2) {
        foreach ($array2 as $key => $values) {
            if (!isset($array1[$key])) {
                $array1[$key] = $values;
            } else {
                $merged = array_merge($values, $array1[$key]);

                $unique = [];
                $uniqueJson = [];

                foreach ($merged as $row) {
                    $rowKey = json_encode($row);
                    if (!in_array($rowKey, $uniqueJson, true)) {
                        $unique[] = $row;
                        $uniqueJson[] = $rowKey;
                    }
                }
                $array1[$key] = $unique;
            }
        }
        return $array1;
    }

    /**
     *  get filled subscriptions from settings
     */
    function getSubscriptions() {
        $request = Application::get()->getRequest();
        $context = $request->getContext();
        $subscriptions = array();
        foreach ($this->_enrichs as $enrich) {
            if ($context->getData($enrich)) {
                $subscriptions[$enrich] = $context->getData($enrich);
            }
        }

        return $subscriptions;
    }

    function getArticleMessages($subscription, $submissionId, $topicKey) {
        $scrollId = null;
        $completed = false;
        $results = array();
        $messages = array();


        while (!$completed) {
            if ($scrollId) {
                $parsedJson = $this->getJSON(null, $scrollId);
            } else {
                $parsedJson = $this->getJSON($subscription, null);
            }

            $topic = array_search($topicKey, $this->_enrichs);

            $completed = $parsedJson['completed'];
            if ($completed || $parsedJson['values'] == null) {
                continue;
            }
            $scrollId = $parsedJson['id'];
            foreach ($parsedJson['values'] as $item) {
                if (!isset($item['originalId'], $item['topic'])) {
                    continue;
                }

                //Topic is not the same as subscrition
                if ($item['topic'] != $topic) {
                    continue;
                }
;
                $oaiTest = strrpos($item['originalId'], 'oai');
                
                if ($oaiTest === false) {
                    continue;
                }
                
                $pos = strrpos($item['originalId'], '/');
                
                if (!$pos) {
                    continue;
                }
                
                $jsonSubmissionId = substr($item['originalId'], $pos + 1);
                
                if(!preg_match('/^-?\d+$/', $jsonSubmissionId)){
                    continue;
                }
                $idToCompare = $pos !== false ? $jsonSubmissionId : $item['originalId'];

                if ($submissionId) {
                    if ($idToCompare == $submissionId) {
                        $results[$submissionId][] = $item;
                    }
                } else {
                    $results[$jsonSubmissionId][] = $item;
                }
            }
        }

        if (sizeof($results) > 0) {
            $messages = $this->resultsToArray($results);
        }
        return $messages;
    }

    function resultsToArray($results) {
        $messages = array();
        $message = "";
        foreach ($results as $submissionId => $submissionResults) {
            foreach ($submissionResults as $result) {
                switch ($result['topic']) {
                    case 'ENRICH/MORE/OPENACCESS_VERSION':
                    case 'ENRICH/MISSING/OPENACCESS_VERSION':
                        $message = __('plugins.generic.openAIREBrokerService.manager.settings.hostedBy') . "<strong>" . $result['message']['instances[0].hostedby'] . "</strong><br /> " . __('plugins.generic.openAIREBrokerService.manager.settings.license') . "<strong>" . $result['message']['instances[0].license'] . "</strong> <br />" . __('plugins.generic.openAIREBrokerService.manager.settings.url') . "<a href='" . $result['message']['instances[0].url'] . "' target='_blank'>" . mb_strimwidth($result['message']['instances[0].url'], 0, 40, '…') . "</a>";
                        break;
                    case 'ENRICH/MISSING/PID':
                    case 'ENRICH/MORE/PID':
                        $message = __('plugins.generic.openAIREBrokerService.manager.settings.type') . "<strong>" . $result['message']['pids[0].type'] . "</strong>; " . __('plugins.generic.openAIREBrokerService.manager.settings.value') . "<strong>" . $result['message']['pids[0].value'] . "<strong>";
                        break;
                    case 'ENRICH/MISSING/AUTHOR/ORCID':
                        $message = __('plugins.generic.openAIREBrokerService.manager.settings.fullname') . "<strong>" . $result['message']['creators[0].fullname'] . "</strong><br /> " . __('plugins.generic.openAIREBrokerService.manager.settings.orcid') . "<strong>" . $result['message']['creators[0].orcid'] . "</strong>";
                        break;
                    case 'ENRICH/MISSING/ABSTRACT':
                        $message = __('plugins.generic.openAIREBrokerService.manager.settings.abstract') . "<strong>" . $result['message']['abstracts[0]'] . "</strong>";
                        break;
                    case 'ENRICH/MISSING/PUBLICATION_DATE':
                        $message = __('plugins.generic.openAIREBrokerService.manager.settings.publicationDate') . "<strong>" . $result['message']['publicationdate'] . "</strong>";
                        break;
                    case 'ENRICH/MISSING/PROJECT':
                        break;
                    case 'ENRICH/MISSING/SUBJECT/DDC':
                    case 'ENRICH/MORE/SUBJECT/DDC':
                    case 'ENRICH/MISSING/SUBJECT/JEL':
                    case 'ENRICH/MORE/SUBJECT/JEL':
                    case 'ENRICH/MISSING/SUBJECT/ACM':
                    case 'ENRICH/MORE/SUBJECT/ACM':
                    case 'ENRICH/MISSING/SUBJECT/MESHEUROPMC':
                    case 'ENRICH/MORE/SUBJECT/MESHEUROPMC':
                    case 'ENRICH/MISSING/SUBJECT/ARXIV':
                    case 'ENRICH/MORE/SUBJECT/ARXIV':
                        $message = __('plugins.generic.openAIREBrokerService.manager.settings.type') . "<strong>" . $result['message']['subjects[0].type'] . "</strong>; " . __('plugins.generic.openAIREBrokerService.manager.settings.value') . "<strong>" . $result['message']['subjects[0].value'] . "</strong>";
                        break;
                    default:
                        break;
                }

                $messages[$submissionId][] = array('enrichmentsTopic' => $result['topic'], 'enrichmentsTrust' => $result['trust'], 'enrichmentsMessage' => $message);
            }
        }
        return $messages;
    }

    /*
     * Get JSON from OpenAIRE
     * @param $subscription string
     * @return array
     */

    function getJSON($subscription, $scrollId) {
        if ($subscription) {
            $jsonUrl = $this->_brokerServiceApi . rawurlencode($subscription);
        } elseif ($scrollId) {
            $jsonUrl = $this->_brokerServiceApiNextpage . rawurlencode($scrollId);
        }
        // open a file and read data
        $jsonFile = file_get_contents($jsonUrl);

        $jsonData = json_decode($jsonFile, true);

        return $jsonData;
    }

}

