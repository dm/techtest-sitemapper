# Sitemapper

Implement a quick crawler and sitemap generator.

Created for a quick technical test.


## Makefile

 * Tests: `make docker-test`


## Implementation

* Dockerise console app
* TDD
* Symfony components


## Notes

 * Should limit to domain and number of pages to crawl...
 * Time won't allow for much, focused on main task deliverable, and documenting decisions being made.
 * Dockerise each components for faster setup/review? At least add `Makefile`!
 * Sitemaps support media (images/video), there might be no time to support these
 * Won't make this horizontally scalable async crawler (Queue service, would be great to add a Python parser and Golang async service broker!


## Limitation

 * Hard limit on 2 hours for the exercise :(
