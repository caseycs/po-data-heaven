# Product Owner Data Heaven

## Project isAbandoned, please use AirBnb Superset instead

That was a good try at that time, but nowadays there are better alternatives. Take a look at https://github.com/airbnb/superset !

## Overall

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

## Report transformers

Sometimes you need some post-processing of SQL. Fot this purpose there are transformers.

Generic example:

```
name: Report name

sql: >
 SELECT * FROM table

transformers:
  - exampleTranformerName1
  - exampleTranformerName2:
      parameter1: value1
      parameter2: value2
```

#### reorderAndFilterColumns

Reorders column to the defined order and drop all others.

```
transformers:
  - reorderAndFilterColumns
      columns: [a, b, c]
```

Input

```
[[d:4, c:3, b:2, a:1]]
```

Output

```
[[a:1, b:2, c:3]]
```

## Cell formatters

Sometime we want a bit more then just a raw value, 

```
name: Report name

sql: >
 SELECT value1, value2 FROM table

columns:
  value1:
    format: formatterName1
  value2:
    format: formatterName2
    options:
        optionName1: optionValue1
        optionName2: optionValue2
```

#### mysqlDate

Reorders column to the defined order and drop all others.

```
columns:
 value:
  format: mysqlDate
  options:
   format: d.m.y
```

Input

```
2010-10-10 10:10:19
```

Output

```
10.10.10
```
