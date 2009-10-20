=== ScholarPress Courseware ===
Contributors: davelester, epistemographer, jeremyboggs, stakats, zgordon
Tags: education, syllabus, courses, bibliography, assignments
Tested up to: 2.6
Stable tag: 1.1.1

ScholarPress Courseware enables you to manage a class with a WordPress blog. The plugin give you the ability to add and edit a schedule, create a bibliography bibliography and assignments, and manage general course information. Designed primarily for use in higher ed courses, but could easily be adapted for other uses.

== Installation ==
1. Unzip the download, and copy the 'scholarpress-courseware' plugin into your wordpress plugins folder, normally located in /wp-content/plugins/. In other words, all files for the plugin should be in wp-content/plugins/scholarpress-courseware/

2. Login to Wordpress Admin and activate the plugin.

3. Using the SP Courseware menu, fill in the appropriate information for your course information, bibliography, assignments, and schedule.

== Documentation ==

Documentation for use can be found at http://scholarpress.net/courseware/.

== Version History ==

= Version 1.1 =
* Update user interface
* More options in the admin panel for controlling output of syllabus (whether to use full or short citations for bibliography items, whether to include descriptions in bibliography, various styles for formatting the schedule)
* Clean up HTML formatting for various plugin outputs (schedule, bibliography, assignments).

= Version 1.0.2 =
* Fixed some bibliography formatting issues and added appropriate CSS. Rename root plugin folder to 'scholarpress-courseware' so it doesn't need to be changed after download.

= Version 1.0.1 =
* Fixed bug that prevented bibliography from being written to the bibliography page.

= Version 1.0 =
* First push to WP-Plugins Directory

== To Do ==

= Version 1.5 =
* Refactors file structure and code, adds helpers for retrieving data easily.
* create an extensible architecture that makes it easy to add new features in the future
* separate administrative panel and public-facing templates
* separate page layouts/designs from helper functions included within them
* Fix javascript toggle on various forms.
* Separate pages for managing entries and adding/editing individual entries for schedule, bibliography, and assignments.
* Add short codes for schedule and bibliography to easily put data into blog posts and pages.
* Make Courseware work with WordPress MU.

= Version 2.0 =
* replace the use of 'pages' with templates for each component - removing confusion from users when a new page was dynamically created that they couldn't edit
* refactor the database to reduce redundancy and significantly decrease load times
* separate installation SQL for each component (core, bibliography, and assignments)
* Add units to group schedule entries together.
* Add feature to bibliography to allow importing of bibliographic items from a standard format (XML, RDF, microformats, others?).
* Add a simple grading system, to allow students to write blog posts that the instructor can grade in the interface.
* Add support for multiple courses.

