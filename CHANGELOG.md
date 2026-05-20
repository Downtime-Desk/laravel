# Changelog

## [2.0.0](https://github.com/Downtime-Desk/laravel/compare/v1.0.0...v2.0.0) (2026-05-20)


### ⚠ BREAKING CHANGES

* implement core reporting and pinging functionality in DowntimeDeskManager
* The configuration key 'webhooks' has been renamed to 'monitors' and several public methods have been renamed.

### Features

* add concurrency control for aggregated pings using cache locks ([77da4ec](https://github.com/Downtime-Desk/laravel/commit/77da4ec02773439dcc8fb5058b475d5338ff751e))
* add reset capability to manager for testing stability ([c0caa07](https://github.com/Downtime-Desk/laravel/commit/c0caa07d82e707f714e9e44383307a7d5fe21682))
* implement core reporting and pinging functionality in DowntimeDeskManager ([2c695b3](https://github.com/Downtime-Desk/laravel/commit/2c695b36c01cf94b35b9cf787d5e1cf21054ec60))
* implement robust ping flushing with batching and error recovery ([8538ce9](https://github.com/Downtime-Desk/laravel/commit/8538ce95f76cde27620e98786a076786a4be0cb1))


### Bug Fixes

* correct monitor configuration keys and security header names ([0ba2925](https://github.com/Downtime-Desk/laravel/commit/0ba292522d211d09e1e1813b55a1c34bc111911c))
* resolve infinite recursion in ServiceProvider singleton registration ([3c2d4e8](https://github.com/Downtime-Desk/laravel/commit/3c2d4e8c5fa362efe4eaa2ed63bebbf3b680c92e))

## 1.0.0 (2026-05-13)


### Features

* initial release ([cf2fa49](https://github.com/Downtime-Desk/laravel/commit/cf2fa49600fd3a79d425ce7dace0ada5825ac847))
