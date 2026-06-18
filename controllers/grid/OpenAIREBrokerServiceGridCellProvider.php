<?php

/**
 * @file plugins/generic/openAIREBrokerService/controllers/grid/OpenAIREBrokerServiceGridCellProvider.php
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class OpenAIREBrokerServiceGridCellProvider
 * @ingroup plugins_generic_openAIREBrokerService
 *
 * @brief Class for a cell provider to display information about funder items
 */

namespace APP\plugins\generic\openAIREBrokerService\controllers\grid;
use PKP\controllers\grid\GridCellProvider;

class OpenAIREBrokerServiceGridCellProvider extends GridCellProvider {

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
			case 'enrichmentsTopic':
				return array('label' => $enrichmentsItem['enrichmentsTopic']);
			case 'enrichmentsTrust':
				return array('label' => $enrichmentsItem['enrichmentsTrust']);
			case 'enrichmentsMessage':
				return array('label' => $enrichmentsItem['enrichmentsMessage']);
		}
	}
}

