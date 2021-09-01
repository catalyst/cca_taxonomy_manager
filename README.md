# CCA Taxonomy Manager

An experimental, Views-based taxonomy manager:
* browse large taxonomies (assumes non-hierarchical vocabularies)
* filter using Search API Database search (offers English stemming)
  * e.g. performer matches perform, performer, performance
* apply Views Bulk Operations actions to terms
* merge multiple terms to a single term
* move term to a different vocabulary

Currently assumes use in the context of Islandora (https://islandora.ca).

## Requirements

This module is a Drupal feature, and requires:

* controlled_access_terms
  * controlled_access_terms_defaults
* csv_serialization
* rest
* search_api
* search_api_db
* search_api_solr
* serialization
* taxonomy
* term_merge
  with patch from https://www.drupal.org/project/term_merge/issues/3089426#comment-14179070 for Create Action for Views Bulk Operations
* user
* views
* views_bulk_operations
* views_data_export


## Installation

If you manage your site dependencies via Composer then this module's dependencies will be installed automatically once the module itself is installed via Composer.

After enabling the module, ensure all terms are indexed in the CCA Taxonomy Manager Term index  /admin/config/search/search-api/index/cca_taxonomy_manager_term_index`

Browe a taxonomy via path such as `/admin/structure/taxonomy/manage/{vocabulary}/cca-search`, e.g. `/admin/structure/taxonomy/manage/person/cca-search`.

Ensure the "Merge taxonomy terms" permission is assigned appropriately.

? Ensure users managing term have Fedora Admin role to persist term changes into fcrepo?


## Maintainers

* Jonathan Hunt - https://www.drupal.org/u/jonathan_hunt
