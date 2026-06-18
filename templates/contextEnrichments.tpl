{**
 * templates/enrichmentss.tpl
 *
 * Copyright (c) 2014-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * OpenAIRE Broker Service plugin  -- displays the list of enrichments.
 *}
<tab id="openAireEnrichments" label="{translate key="plugins.generic.openAIREBrokerService"}">
        <p>{translate key="plugins.generic.openAIREBrokerService.article.description"}</p>
    
        {capture assign=openAIREBrokerServiceContextGridUrl}{url router=PKP\core\PKPApplication::ROUTE_COMPONENT component="plugins.generic.openAIREBrokerService.controllers.grid.OpenAIREBrokerServiceContextGridHandler" op="fetchGrid" escape=false}{/capture}
	{load_url_in_div id="openAIREBrokerServiceContextGridContainer" url=$openAIREBrokerServiceContextGridUrl}
</tab>
