## Informtion ##

This is a subquery library for CodeIgniter’s (1.7.x and 2.x) active record class.  It lets you use active record methods to create subqueries in SQL queries.
It supports SELECT, JOIN, FROM (and other statements). It also supports UNION ALL.  You can have subqueries inside subqueries inside UNIONS, etc.

## Instructions ##

Put `Subquery.php` into application/libraries, then load it in your code.  You can add `subquery` to `$autoload['libraries']` in application/config/autoload.php, or load it by calling `$this->load->library('Subquery')`.

## Methods ##

**start_subquery**: Creates a new database object to be used for the subquery  
*Parameters*:

 - $statement: SQL statement to put subquery into (select, from, join, etc.)
 - $join_type: JOIN type (only for join statements)
 - $join_on: JOIN ON clause (only for join statements)

*Returns*: A new database object to use for subqueries

**start_union**: Creates a new database object to be used for unions  
*Parameters*: None

*Returns*: A new database object to use for a union query  
***Note***: Please do all 'ORDER BY' or other modifiers BEFORE start_union

**end_subquery**: Closes the database object and writes the subquery  
*Parameters*:

 - $alias: Alias to use in query

*Returns*: None

**end_union**: Combines all opened db objects into a UNION ALL query  
*Parameters*: None

*Returns*: None
