<?php

/**
 * @file plugins/generic/openAIREBrokerService/controllers/grid/OpenAIREBrokerServiceContextGridCellProvider.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class OpenAIREBrokerServiceContextGridCellProvider
 * @ingroup plugins_generic_openAIREBrokerService
 *
 * @brief Class for a cell provider to display information about funder items
 */

namespace APP\plugins\generic\openAIREBrokerService\controllers\grid;
use PKP\controllers\grid\GridCellProvider;

class OpenAIREBrokerServiceContextGridCellProvider extends GridCellProvider {

	//
	// Template methods from GridCellProvider
	//

	/**
	 * Extracts variables for a given column from a data element
	 * so that they may be assigned to template before rendering.
	 *
	 * @copydoc GridCellProvider::getTemplateVarsFromRowColumn()
	 */
	function getTemplateVarsFromRowColumn($row, $column) {
		$enrichmentsItem = $row->getData();
		switch ($column->getId()) {
                        case 'id':
				return array('label' => $enrichmentsItem['id']);
			case 'title':
                                return array('label' => $enrichmentsItem['title']);
                        case 'issue':
                                return array('label' => $enrichmentsItem['issue']);
			case 'enrichmentsTopic':
				return array('label' => str_replace('ENRICH/','',$enrichmentsItem['enrichmentsTopic']));
			case 'enrichmentsMessage':
                                $message = __("plugins.generic.openAIREBrokerService.trust") . ": " . $enrichmentsItem['enrichmentsTrust']. "<br />".$enrichmentsItem['enrichmentsMessage'];                                    
				return array('label' => $message);
		}
	}        
}

