# Product Owner Data Heaven

[![Build Status](https://travis-ci.org/caseycs/po-data-heaven.svg?branch=master)](https://travis-ci.org/caseycs/po-data-heaven)

Fast and simple way to build reports against relational data. Inspired by hours or investigating
suspicious use cases with the product owner.

## Requirements

* PHP 5.6
* PDO data source

## Reports YML examples

A simplest one:

```
name: Consumer details

sql: >
 SELECT * FROM `user` WHERE id IN (:id)

parameters:
  id:
```

A complicated one:


```
name: Consumer details

description: bla bla bla

sql: >
 SELECT id, name, age, company_id, tmp FROM `user`
 WHERE company_id = :company_id AND type = :type

parameters:
  company_id:
    name: id of tid company
    idOfEntity: company
  type:
    name: user type

order: name ASC

limit: 100

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
    
transformers:
  - firstColumnRotate:
  - addTwigColumn:
      after: id
      name: nameAndAge
      template: "{{ row['name'] }} AGE {{ row['age'] }}"
  - removeColumn:
      column: tmp
```
