<?php

/**
 * @file OpenAIREBrokerServiceSettingsForm.php
 *
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class OpenAIREBrokerServiceSettingsForm
 * @ingroup plugins_generic_openAIREBrokerService
 *
 * @brief Form for journal managers to modify OpenAIRE Broker Service plugin settings
 */

namespace APP\plugins\generic\openAIREBrokerService;

use APP\core\Application;
use APP\journal\JournalDAO;
use APP\template\TemplateManager;
use PKP\db\DAORegistry;
use PKP\form\Form;

class OpenAIREBrokerServiceSettingsForm extends Form {

    /** @var int */
    var $_contextId;

    /** @var object */
    var $_plugin;

    /** @var context * */
    var $_context;

    const CONFIG_VARS = array(
        'enrich_more_openaccess_version' => 'string',
        'enrich_more_pid' => 'string',
        'enrich_missing_author_orcid' => 'string',
        'enrich_missing_pid' => 'string',
        'enrich_missing_abstract' => 'string',
        'enrich_missing_subject_ddc' => 'string',
        'enrich_more_subject_ddc' => 'string',
        'enrich_missing_subject_jel' => 'string',
        'enrich_more_subject_jel' => 'string',
        'enrich_missing_publication_date' => 'string',
        'enrich_missing_openaccess_version' => 'string',
        'enrich_missing_subject_acm' => 'string',
        'enrich_more_subject_acm' => 'string',
        'enrich_missing_project' => 'string',
        'enrich_missing_subject_mesheuropmc' => 'string',
        'enrich_more_subject_mesheuropmc' => 'string',
        'enrich_missing_subject_arxiv' => 'string',
        'enrich_more_subject_arxiv' => 'string'
    );

    /**
     * Constructor
     * @param $plugin object
     * @param $context int
     */
    function __construct($plugin, $context) {
        $this->_plugin = $plugin;
        $this->_context = $context;

        parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));
        $this->addCheck(new \PKP\form\validation\FormValidatorPost($this));
        $this->addCheck(new \PKP\form\validation\FormValidatorCSRF($this));
    }

    /**
     * Initialize form data.
     */
    function initData() {
        $this->_data = array();
        $context = $this->_context;
        foreach (self::CONFIG_VARS as $configVar => $type) {
            $this->_data[$configVar] = $context->getData($configVar);
        }
    }

    /**
     * Assign form data to user-submitted data.
     */
    function readInputData() {
        $this->readUserVars(array_keys(self::CONFIG_VARS));
    }

    /**
     * @copydoc Form::fetch()
     */
    function fetch($request, $template = null, $display = false) {
        $templateMgr = TemplateManager::getManager($request);

        $templateMgr->assign('pluginName', $this->_plugin->getName());
        return parent::fetch($request, $template, $display);
    }

    /**
     * @copydoc Form::execute()
     */
    function execute(...$functionArgs) {
        $context = $this->_context;

        foreach (self::CONFIG_VARS as $configVar => $type) {
            $context->setData($configVar, $this->getData($configVar));
        }
        parent::execute(...$functionArgs);

        $contextDao = DAORegistry::getDAO('JournalDAO'); /* @var $contextDao JournalDAO */
        $contextDao->updateObject($context);
    }

}

