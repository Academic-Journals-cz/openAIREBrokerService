{**
* plugins/generic/openAIREBrokerService/templates/settingsForm.tpl
*
* Copyright (c) 2014-2020 Simon Fraser University
* Copyright (c) 2003-2020 John Willinsky
* Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
*
* OpenAIREBrokerService plugin settings
*
*}
<script type="text/javascript">
    $(function () {ldelim}
            // Attach the form handler.
            $('#openAIREBrokerServiceSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
    {rdelim});
</script>
<form class="pkp_form" id="openAIREBrokerServiceSettingsForm" method="post" action="{url router=PKP\core\PKPApplication::ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
    {csrf}
    {fbvFormArea id="openAIREBrokerServiceSettingsFormSubscriptionArea" title="plugins.generic.openAIREBrokerService.manager.settings.subscriptions"}
            {translate key="plugins.generic.openAIREBrokerService.manager.settings.subscriptions.description"}
            <br />
            <br />
            {fbvFormSection for="enrich_more_openaccess_version" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_more_openaccess_version"}
                    {fbvElement type="text" id="enrich_more_openaccess_version" value=$enrich_more_openaccess_version size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_more_pid" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_more_pid"}
                    {fbvElement type="text" id="enrich_more_pid" value=$enrich_more_pid size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_missing_author_orcid" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_missing_author_orcid"}
                    {fbvElement type="text" id="enrich_missing_author_orcid" value=$enrich_missing_author_orcid size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_missing_pid" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_missing_pid"}
                    {fbvElement type="text" id="enrich_missing_pid" value=$enrich_missing_pid size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_missing_abstract" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_missing_abstract"}
                    {fbvElement type="text" id="enrich_missing_abstract" value=$enrich_missing_abstract size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_missing_subject_ddc" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_missing_subject_ddc"}
                    {fbvElement type="text" id="enrich_missing_subject_ddc" value=$enrich_missing_subject_ddc size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_more_subject_ddc" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_more_subject_ddc"}
                    {fbvElement type="text" id="enrich_more_subject_ddc" value=$enrich_more_subject_ddc size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_missing_subject_jel" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_missing_subject_jel"}
                    {fbvElement type="text" id="enrich_missing_subject_jel" value=$enrich_missing_subject_jel size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_more_subject_jel" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_more_subject_jel"}
                    {fbvElement type="text" id="enrich_more_subject_jel" value=$enrich_more_subject_jel size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_missing_publication_date" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_missing_publication_date"}
                    {fbvElement type="text" id="enrich_missing_publication_date" value=$enrich_missing_publication_date size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_missing_openaccess_version" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_missing_openaccess_version"}
                    {fbvElement type="text" id="enrich_missing_openaccess_version" value=$enrich_missing_openaccess_version size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_missing_subject_acm" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_missing_subject_acm"}
                    {fbvElement type="text" id="enrich_missing_subject_acm" value=$enrich_missing_subject_acm size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_more_subject_acm" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_more_subject_acm"}
                    {fbvElement type="text" id="enrich_more_subject_acm" value=$enrich_more_subject_acm size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_missing_project" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_missing_project"}
                    {fbvElement type="text" id="enrich_missing_project" value=$enrich_missing_project size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_missing_subject_mesheuropmc" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_missing_subject_mesheuropmc"}
                    {fbvElement type="text" id="enrich_missing_subject_mesheuropmc" value=$enrich_missing_subject_mesheuropmc size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_more_subject_mesheuropmc" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_more_subject_mesheuropmc"}
                    {fbvElement type="text" id="enrich_more_subject_mesheuropmc" value=$enrich_more_subject_mesheuropmc size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_missing_subject_arxiv" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_missing_subject_arxiv"}
                    {fbvElement type="text" id="enrich_missing_subject_arxiv" value=$enrich_missing_subject_arxiv size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            {fbvFormSection for="enrich_more_subject_arxiv" title="plugins.generic.openAIREBrokerService.manager.settings.enrich_more_subject_arxiv"}
                    {fbvElement type="text" id="enrich_more_subject_arxiv" value=$enrich_more_subject_arxiv size=$fbvStyles.size.LARGE}     
            {/fbvFormSection} 
            
    {/fbvFormArea}     
    {fbvFormButtons submitText="common.save"}
</form>
