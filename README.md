# Sitemapper

Implement a quick crawler and sitemap generator.

Created for a quick technical test.

Will grab a pseudo-random Neocities site by default (It's the future of the Interwebs, _make the web fun again_!!!)


## Easy run

Assumptions:
 * Running on a \*nix based OS
 * [Docker is installed](https://docs.docker.com/engine/installation/)
 * Grab a nice cup of coffee
 * `make`
 * ???
 * Profit!


## Makefile

 * Tests (Docker): `make docker-test`
 * Tests: `make test`
 * Run (Docker): `make docker-run URL=https://2bit.neocities.org/`
 * Run: `make run URL=https://2bit.neocities.org/`


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

 * Hard limit on 2 hours, which is severely limiting.
 * Will take the "please don’t spend much more than that", and have a little leeway...
