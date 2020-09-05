# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
## [0.8.2] 2020-09-05
- Updated docker files to pick up templates
## [0.8.0] 2020-09-05
- Added Swagger Docs documentation decorator for JWT Authentication endpoint.
## [0.7.0] 2020-09-04
- Added Github Actions
## [0.6.4]
- removing unsed `Http\Requests` folder path. 
## [0.6.3]
- removing unneeded dependency 
## [0.6.1]
- fixing github actions
## [0.6.0]
- Added github actions

## [0.5.0] 2020-08-25
- Added support for Workflows for Shopping Lists
- Added new endpoints for transitioning shopping lists state
- added new endpoint for getting list of available states

## [0.4.1] 2020-08-23
- Added fix for logging to send to stderr instead of file.
 
## [0.4.0]
- Updated dev docker-composer to not mount vendor folder externally.

## [0.3.0] 2020-08-22
- Added new copy operation on ShoppingLists resource to allow cloning of Template shopping lists. 

## [0.2.1] 2020-08-22
- Added Filters to the endpoint for ShoppingLists and ShoppingItems resources.

## [0.2.0]
### Added
- Added new entity ShoppingList with CRUD endpoints
- Added relationship between ShoppingList and Shopping Item. 

## [0.1.0] - 2020-08-21
### Added
- Added new Entity ShoppingItem which is a list of items a person usually shops. 
- New ShoppingItem API GET, POST, PUT, DELETE and PATCH added. 
- Added new Entity PreWrite listen to assign Owner from JWT Token 
- Added new `@OwnerAware` annotation for marking entities that should be filtered by owner
- Added `OwnerFilter` that will is automatically triggered to filter objects by owner
- `DoctrineOwnerFilterKernelEvent` added to enable `OwnerFilter` when `Authorization` token is present.
- `EntityOwnerPreWriteSubscriber` added to set `owner` fields on Entities with `OwnerAware` annotation.