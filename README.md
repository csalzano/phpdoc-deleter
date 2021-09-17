# phpdoc-deleter
An add-on for phpdoc-parser that deletes previously-generated documentation that is no longer updated by parser runs.

Learn how I use phpdoc-parser and this plugin to create WordPress.org-style documentation for one of my projects: https://coreysalzano.com/wordpress/mimic-the-wordpress-org-developer-reference/

## Filter Hooks

 - **phpdoc_deleter_force_delete**

   Filters a boolean value that determines whether or not posts will skip trash and be force deleted. Defaults to true.

 - **phpdoc_deleter_post_types**

   Filters an array of post type names from which the deleted posts will be found. Defaults to:

    - wp-parser-function
    - wp-parser-method
    - wp-parser-class
    - wp-parser-hook

 - **phpdoc_deleter_taxonomies**

   Filters an array of taxonomy names from which empty terms will be deleted. Defaults to: 

    - wp-parser-namespace
    - wp-parser-package
    - wp-parser-since
    - wp-parser-source-file

## Known Limitations

This plugin operates as if `phpdoc-parser` is only generating docs for a single project. Posts and empty terms will be deleted at the end of each run if they are not touched by that run.