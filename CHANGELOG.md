# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
## [0.2.1]
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