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
* term_reference_change
  with patch from https://git.drupalcode.org/project/term_reference_change/-/merge_requests/2.patch for merging references in typed_relation fields
* user
* views
* views_bulk_operations
* views_data_export


## Installation

If you manage your site dependencies via Composer then this module's dependencies will be installed automatically once the module itself is installed via Composer.

After enabling the module, ensure all terms are indexed in the CCA Taxonomy Manager Term index  `/admin/config/search/search-api/index/cca_taxonomy_manager_term_index`

Browe a taxonomy via path such as `/admin/structure/taxonomy/manage/{vocabulary}/cca-search`, e.g. `/admin/structure/taxonomy/manage/person/cca-search`.

Ensure the "Merge taxonomy terms" permission is assigned appropriately.

@todo: check if it is necessary to ensure users managing terms have Fedora Admin role to persist term changes into fcrepo.

## Guide

1. To move a term, browse or search within `/admin/structure/taxonomy/manage/{vocabulary}/cca-search` to locate the term(s).
1. Check the (views bulk operations) checkbox on left of terms table.
1. Choose "Move term" option from Action select widget.
1. Click "Apply to selected items" button.
1. Choose target vocabulary from select widget.
1. Click "Apply" button.
1. A batch process will start, "Performing Move term on selected entities"...

_Warning: term move does not account for hierarchies, nor does it account for the possible difference in fields assigned to taxonomy terms in different vocabularies._

@todo: define what will survive a move, e.g. term name, description.

1. To merge one or more terms, browse or search within `/admin/structure/taxonomy/manage/{vocabulary}/cca-search` to locate the term(s) to be merged (but not the target term).
1. Check the (views bulk operations) checkbox on left of terms table.
1. Choose "Merge terms" option from Action select widget.
1. Click "Apply to selected items" button.
1. Choose target term from Existimg term autocomplete widget.
1. Click "Apply" button.

_Warning: the patch for term_reference_change must be applied in order to update reference in typed_relation fields (e.g. Islandora Object Linked Agent field)._

_Warning: redirects are not added automatically, e.g. from old term to target term._


## Known issues

* Page title on cca-search view is vocabulary machine name, not human label.


## Maintainers

* Jonathan Hunt - https://www.drupal.org/u/jonathan_hunt
