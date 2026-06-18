# OpenAIRE Broker API – Enrichments

The OpenAIRE Broker API – Enrichments plugin allows editors in OJS to view enrichment suggestions provided by the [OpenAIRE Broker API](https://graph.openaire.eu/docs/apis/broker-api/).

Enrichments are not automatically imported or written into article metadata. Instead, the plugin visualizes enrichment results for registered subscriptions via the [OpenAIRE Provide service](https://provide.openaire.eu/home).

- You can explore and test the API here: **http://api.openaire.eu/broker/swagger-ui/index.html**

# Features:
- Adds a tab in **Website Settings** with a list of all enrichments available for the journal.
- Displays **enrichments per article** in the **Publication** inside Workflow.
- Connects to **OpenAIRE Broker API** based on your journal's **subscriptions** in **OpenAIRE Provide**.
- The plugin is **read-only**: enrichments are displayed but not stored or edited.
- **Note:** API responses may load slowly, especially for journals with large datasets.

# Notes on OJS 3.5 / 3.6
OJS 3.5 rebuilt the editorial workflow as a Vue single-page application and removed
the server-side template hook (`Template::Workflow::Publication`) that earlier versions
used to add the article-level enrichments tab. This release integrates with the new
workflow through the front-end extension API that OJS 3.5 and 3.6 share (they use the
same ui-library base):

- The plugin registers a Vue component and extends the `workflow` Pinia store via
  `pkp.registry` (`registerComponent`, `storeExtend` + the store `extender`). It adds a
  single **OpenAIRE enrichments** entry under the workflow's Publication navigation
  (enrichments are per article, so there is one entry per submission, not one per version);
  selecting it shows the article's enrichments. The panel reuses the existing read-only
  grid (`OpenAIREBrokerServiceGridHandler`), so no separate API is required. The entry is
  shown to users on the editorial dashboard regardless of whether they are an assigned
  editor on the submission — access to the data is still enforced server-side by the grid
  handler. The relevant code is in `OpenAIREBrokerServicePlugin::addWorkflowEnrichmentsTab()`
  and `js/workflowEnrichments.js`.
- The **journal-level** grid (**Settings → Website → OpenAIRE Enrichments**) lists the same
  per-article data (id, title, issue, topic, message) and works independently of the
  workflow integration.

The front-end integration uses documented extension points (see the PKP ui-library
"Guide/Plugins" and workflow page documentation) and is written defensively, but the
workflow UI can still change between releases; if the entry does not appear, check the
browser console and `pkp.registry.getPiniaStore('workflow').extender.listExtendableFns()`.

# Screenshots
![Journal-level enrichments](https://munispace.muni.cz/public/craft-oa/enrichments-tab.png)
![Article-level enrichments](https://munispace.muni.cz/public/craft-oa/article-enrichments.png)

# License
This plugin is licensed under the GNU General Public License v3. See the file LICENSE for the complete terms of this license.

# System Requirements
OJS 3.5.x (and PHP 8.2 or later, as required by OJS 3.5).

For older platforms use the matching branch: `stable-3_4_0` (OJS 3.4), `stable-3_3_0` (OJS 3.3), `stable-3_2_1` (OJS 3.2).

# Version History
- Version 3.5.0.2 – Workflow enrichments shown per submission, independent of editor assignment
- Version 3.5.0.1 – OJS 3.5/3.6 workflow integration via the Vue extension API (`pkp.registry`)
- Version 3.5.0.0 – Support for OJS 3.5.0
- Version 3.4.0.0 – Support for OJS 3.4.0
- Version 3.3.0.0 – Support for OJS 3.3.0
- Version 3.2.0.0 – Support for OJS 3.2.0

# Installation
Installing using a release from GitHub:
1.	Download the latest compatible release and unzip it.
2.	Move the **openAIREBrokerService** folder to the OJS **plugins/generic/** folder.
3.  Run the update command from the command line in the OJS root folder: 
**php tools/update.php update**
4.	Go to **Settings → Website → Plugins → Generic Plugin → OpenAIRE Broker Service** and enable the plugin.

# Third-Party Software:
This plugin communicates with:

- [OpenAIRE Broker API] (https://graph.openaire.eu/docs/apis/broker-api/)
- [OpenAIRE Provide Service] (https://provide.openaire.eu/)

To receive enrichments, you must first register your journal in **OpenAIRE Provide** and subscribe to enrichment services.

# Credit
---------------
This plugin was developed at the [Masaryk University Press - Munipress](https://www.press.muni.cz), as part of its active participation in the [Craft-OA project](https://www.craft-oa.eu/).

The development was initiated, coordinated, and technically supported by Munipress.

