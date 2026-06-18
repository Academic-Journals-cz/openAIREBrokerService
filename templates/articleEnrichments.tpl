{**
* plugins/generic/openAIREBrokerService/templates/articleEnrichments.tpl
*
* Copyright (c) 2014-2020 Simon Fraser University
* Copyright (c) 2003-2020 John Willinsky
* Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
*
* Article-level OpenAIRE enrichments (role-gated). The grid is loaded from the
* component router. In OJS 3.5 this is injected into the editorial workflow by
* js/workflowEnrichments.js; the markup is kept here for server-side reuse.
*}

{if array_intersect(array(PKP\security\Role::ROLE_ID_MANAGER, PKP\security\Role::ROLE_ID_SUB_EDITOR), (array)$userRoles)}
    <div id="enrichmentsArticleList">
        <p>{translate key="plugins.generic.openAIREBrokerService.article.description"}</p>

        {capture assign=openAIREBrokerServiceGridUrl}{url router=PKP\core\PKPApplication::ROUTE_COMPONENT component="plugins.generic.openAIREBrokerService.controllers.grid.OpenAIREBrokerServiceGridHandler" op="fetchGrid" submissionId=$submission->getId() escape=false}{/capture}
        {load_url_in_div id="openAIREBrokerServiceGridContainer" url=$openAIREBrokerServiceGridUrl}
    </div>
{/if}