# Product Owner Data Heaven

[![Build Status](https://travis-ci.org/caseycs/po-data-heaven.svg?branch=master)](https://travis-ci.org/caseycs/po-data-heaven)

Fast and simple way to build reports against relational data. Inspired by hours or investigating
suspicious use cases with product owner.

## Requirements

* PHP 5.6
* PDO data source

## Reports YML examples

A simplest one:

```
name: Consumer details

description: bla bla bla

sql: >
 SELECT * FROM `user` WHERE id IN (:id)

parameters:
  id:
    name: user_id
```

A complicated one:

name: Consumer details

description: bla bla bla

sql: >
 SELECT * FROM `user` WHERE company_id = :id AND type = :type

parameters:
  company_id:
    name: id of the company
    idOfEntity: company
  type:
    name: user type

columns:
  created_at:
    format: mysqlDate
  profile_url:
    format: url
  group_id:
    idOfEntities: group
  tag_id:
    idOfEntities: [user_tag, company_tag]
  description:
    format: truncate
```
