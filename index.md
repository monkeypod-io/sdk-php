---
layout: default
title: Introduction
nav_order: 1
---

## Welcome!
This is the official PHP library for communicating with the [MonkeyPod](https://monkeypod.io) API. 

The SDK provides useful abstractions of many core MonkeyPod resources. If you use it, you can 
probably avoid ever messing around with the underlying API documentation. If you do need to 
consult that "raw" API documentation, it can currently be found in the 
[MonkeyPod Knowledgebase](https://monkeypod.helpscoutdocs.com/category/134-api?sort=).

## Releases / Stability
Releases prior to 1.0 should be considered unstable and make change at any time. If 
you are using these libraries in production, make sure you have good test coverage and
confirm that everything still works after any upgrades.

## Table of Contents
* [Installation and Setup](installation_and_setup)
  - [Laravel (optional)](laravel)
* [API Resources](resources)
  - [Constructors](resources#Constructors)
  - [Required, optional, and additional/unlisted fields](resources#required,+optional,+and+additional/unlisted+fields)
  - [Resource Collections](resources#resource+collections)
    - [Pagination](resources#pagination)
  - Available Resources
    - [Entities (a.k.a. "Relationships")](resources/entities)
    - [Custom Attributes](resources/custom_attributes)
    - [Interactions](resources/interactions)
* Future Development
  - Accounts
    - Retrieve account
    - Retrieve collection of accounts
      - Filtered by type and/or subtype
      - Include inactive
  - Classes
    - Retrieve class
    - Retrieve collection of classes
  - Donation
    * Retrieve a donation
    * Create a donation
    * Update a donation
    * Delete a donation
  * Items
    * Retrieve item
    * Retrieve collection of items
  * Pipelines
    * Add relationship to pipeline
    * Update a relationship's status for a pipeline step
  * Sales
    * Retrieve a sale
    * Create a sale
    * Update a sale
    * Delete a sale
  * Tags
    * Retrieve tag
    * Retrieve collection of tags
      * All tags
      * By type 
  * Webhooks
    * Retrieve a collection of webhooks
    * Create a webhook
    * Delete a webhook
  * Events
    * Retrieve a collection of webhook-capable events
