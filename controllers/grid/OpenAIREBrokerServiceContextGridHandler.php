<?php

/**
 * @file plugins/generic/openAIREBrokerService/controllers/grid/OpenAIREBrokerServiceContextGridHandler.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class OpenAIREBrokerServiceContextGridHandlers
 * @ingroup plugins_generic_openAIREBrokerService
 *
 * @brief Handle OpenAIREBrokerService Contextg grid requests.
 */

namespace APP\plugins\generic\openAIREBrokerService\controllers\grid;

use PKP\db\DAORegistry;
use APP\facades\Repo;
use PKP\security\Role;
use PKP\controllers\grid\GridColumn;
use PKP\security\authorization\ContextAccessPolicy;
use APP\plugins\generic\openAIREBrokerService\controllers\grid\OpenAIREBrokerServiceContextGridCellProvider;
use APP\plugins\generic\openAIREBrokerService\classes\OpenAIREBrokerServiceEnrichments;

class OpenAIREBrokerServiceContextGridHandler extends \PKP\controllers\grid\GridHandler {

    static $plugin;

    /** @var boolean */
    var $_readOnly;

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        $this->addRoleAssignment(
                array(Role::ROLE_ID_MANAGER, Role::ROLE_ID_SUB_EDITOR),
                array('fetchGrid', 'fetchRow')
        );
    }

    //
    // Getters/Setters
    //

    /**
     * Set the Funder plugin.
     * @param $plugin FundingPlugin
     */
    static function setPlugin($plugin) {
        self::$plugin = $plugin;
    }

    /**
     * Get whether or not this grid should be 'read only'
     * @return boolean
     */
    function getReadOnly() {
        return $this->_readOnly;
    }

    /**
     * Set the boolean for 'read only' status
     * @param boolean
     */
    function setReadOnly($readOnly) {
        $this->_readOnly = $readOnly;
    }

    //
    // Overridden template methods
    //

    /**
     * @copydoc PKPHandler::authorize()
     */
    function authorize($request, &$args, $roleAssignments) {
        $this->addPolicy(new ContextAccessPolicy($request, $roleAssignments));
        return parent::authorize($request, $args, $roleAssignments);
    }

    /**
     * @copydoc Gridhandler::initialize()
     */
    function initialize($request, $args = null) {
        parent::initialize($request, $args);
        $context = $request->getContext();

        // Set the grid details.
        $this->setTitle('plugins.generic.openAIREBrokerService');
        $this->setEmptyRowText('plugins.generic.openAIREBrokerService.noData');

        // Get the items and add the data to the grid
        $openAIREBrokerServiceEnrichments = new OpenAIREBrokerServiceEnrichments();
        $contextEnrichments = $openAIREBrokerServiceEnrichments->contextEnrichments();

        $gridData = $this->setGridDataEnrichments($contextEnrichments, $context);
        $this->setGridDataElements($gridData);
        $this->setReadOnly(true);
        // Columns
        $cellProvider = new OpenAIREBrokerServiceContextGridCellProvider();
        $this->addColumn(
                new GridColumn(
                        'id',
                        null,
                        __('common.id'),
                        'controllers/grid/gridCell.tpl',
                        $cellProvider,
                        array('alignment' => GridColumn::COLUMN_ALIGNMENT_LEFT,
                    'width' => 4)
                )
        );
        $this->addColumn(
                new GridColumn(
                        'title',
                        'grid.submission.itemTitle',
                        null,
                        null,
                        $cellProvider,
                        array('html' => true,
                    'alignment' => GridColumn::COLUMN_ALIGNMENT_LEFT, 'width' => 25)
                )
        );
        $this->addColumn(
                new GridColumn(
                        'issue',
                        'issue.issue',
                        null,
                        null,
                        $cellProvider,
                        array('alignment' => GridColumn::COLUMN_ALIGNMENT_LEFT,
                    'width' => 5)
                )
        );
        $this->addColumn(new GridColumn(
                        'enrichmentsTopic',
                        'plugins.generic.openAIREBrokerService.topic',
                        null,
                        'controllers/grid/gridCell.tpl',
                        $cellProvider,
                        array('alignment' => GridColumn::COLUMN_ALIGNMENT_LEFT,
                    'width' => 8)
        ));
//        $this->addColumn(new GridColumn(
//                        'enrichmentsTrust',
//                        'plugins.generic.openAIREBrokerService.trust',
//                        null,
//                        'controllers/grid/gridCell.tpl',
//                        $cellProvider
//        ));
        $this->addColumn(new GridColumn(
                        'enrichmentsMessage',
                        'plugins.generic.openAIREBrokerService.message',
                        null,
                        'controllers/grid/gridCell.tpl',
                        $cellProvider,
                        array('html' => true, 'alignment' => GridColumn::COLUMN_ALIGNMENT_LEFT, 'width' => 20)
        ));
    }

    function setGridDataEnrichments($contextEnrichments, $context) {
        $gridData = array();
        foreach ($contextEnrichments as $submissionId => $articleEnrichmentsById) {
            foreach ($articleEnrichmentsById as $articleEnrichment) {
                $submission = Repo::submission()->get($submissionId);
                if(!$submission) continue;
                
                $title = $submission->getLocalizedTitle();
                if (empty($title))
                    $title = __('common.untitled');

                if ($submission && !empty($submission->getCurrentPublication())) {
                    $publication = $submission->getCurrentPublication();
                    $authorsInTitle = $publication->getShortAuthorString();
                    $title = $submission->getCurrentPublication()->getLocalizedData('title');
                    $title = $authorsInTitle . '; ' . $title;
                } else {
                    continue;
                }

                $issueId = $submission->getCurrentPublication()->getData('issueId');
                
                $issue = Repo::issue()->get($issueId);
                if ($issue) {
                    $issueIdentification = htmlspecialchars($issue->getIssueIdentification());
                } else {
                    $issueIdentification = "";
                }

                $gridData[] = array(
                    'id' => $submissionId,
                    'title' => htmlspecialchars($title),
                    'issue' => $issueIdentification,
                    'enrichmentsTopic' => $articleEnrichment['enrichmentsTopic'],
                    'enrichmentsTrust' => $articleEnrichment['enrichmentsTrust'],
                    'enrichmentsMessage' => $articleEnrichment['enrichmentsMessage']
                );
            }
        }
        return $gridData;
    }

}

if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\openAIREBrokerService\controllers\grid\OpenAIREBrokerServiceContextGridHandler', '\OpenAIREBrokerServiceContextGridHandler');
}