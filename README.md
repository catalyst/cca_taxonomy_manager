# CCA Taxonomy Manager

An experimental, Views-based taxonomy manager:
* browse large taxonomies (assumes non-hierarchical vocabularies)
* filter using Search API Database search (offers English stemming)
  * e.g. performer matches perform, performer, performance
* apply Views Bulk Operations actions to terms
* merge multiple terms to a single term
* move term to a different vocabulary

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
* user
* views
* views_bulk_operations
* views_data_export


## Installation

If you manage your site dependencies via Composer then this module's dependencies will be installed automatically once the module itself is installed via Composer.

## Maintainers

* Jonathan Hunt - https://www.drupal.org/u/jonathan_hunt
