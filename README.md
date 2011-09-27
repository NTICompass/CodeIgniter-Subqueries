Active Record Subqueries
========================

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

 - $alias - Alias to use in query, or field to use for WHERE
 - $operator - Operator to use for WHERE (=, !=, <, etc.)/WHERE IN (TRUE for WHERE IN, FALSE for WHERE NOT IN) (optional)
 - $database - Database object to use when dbStack is empty (optional)

*Returns*: None

**end_union**: Combines all opened db objects into a UNION ALL query  
*Parameters*:

 - $database - Database object to use when dbStack is empty (optional)

*Returns*: None

**defaultDB**: Sets the default database object to use  
*Parameters*:

 - $database: Default database

*Returns*: None

**join_range**: Helper function to CROSS JOIN a list of numbers (From [this][1] StackOverflow answer)  
*Parameters*:

 - $start: Range start
 - $end: Range end
 - $alias: Alias for number list
 - $table_name: JOINed tables need an alias (Optional)
 - $database - Database object to use when dbStack is empty (optional)

*Returns*: None

## Examples ##

**Subquery in a SELECT statement**  
*SQL*:

    SELECT `word`, (SELECT `number` FROM (`numbers`) WHERE `numberID` = 2) AS number FROM (`words`) WHERE `wordID` = 3

*Active Record*:

    $this->db->select('word')->from('words')->where('wordID', 3);
    $sub = $this->subquery->start_subquery('select');
    $sub->select('number')->from('numbers')->where('numberID', 2);
    $this->subquery->end_subquery('number'); 

**Subquery in a WHERE statement**  
*SQL*:

    SELECT `test`, `test2` FROM table WHERE id IN (SELECT IDs FROM idTable WHERE date = '2011-07-10')

*Active Record*:

    $this->db->select('test');
    $this->db->select('test2');
    $this->db->from('table');
    $sub = $this->subquery->start_subquery('where_in');
    $sub->select('IDs');
    $sub->from('idTable');
    $sub->where('date', '2011-07-10');
    $this->subquery->end_subquery('id');
    
**Subquery in a WHERE statement**  
*SQL*:

    SELECT `test`, `test2` FROM table WHERE id = (SELECT IDs FROM idTable WHERE date = '2011-07-10' AND name = 'Eric')

*Active Record*:

    $this->db->select('test');
    $this->db->select('test2');
    $this->db->from('table');
    $sub = $this->subquery->start_subquery('where');
    $sub->select('IDs');
    $sub->from('idTable');
    $sub->where('date', '2011-07-10');
    $sub->where('name', 'Eric');
    $this->subquery->end_subquery('id');

**Subquery in a FROM statement**  
*SQL*:

    SELECT `test`, `test2` FROM ((SELECT 3 AS test) AS testing, (SELECT 4 AS test2) AS testing2) 

*Active Record*:

    $this->db->select('test');
    $sub = $this->subquery->start_subquery('from');
    $sub->select('3 AS test', false);
    $this->subquery->end_subquery('testing');
    $this->db->select('test2');
    $sub = $this->subquery->start_subquery('from');
    $sub->select('4 AS test2', false);
    $this->subquery->end_subquery('testing2');

**Subquery in a JOIN statement**  
*SQL*:

    SELECT `test`.`a`, `t`.`b`, `test`.`field`
    FROM `test`
    LEFT JOIN (SELECT `ID`,`b` FROM `test2` WHERE `date` > '2011-01-01') AS `t` ON `t`.`ID` = `test`.`ID`
    WHERE `test`.`field` = 4

*Active Record*:

    $this->db->select('test.a, t.b, test.field');
    $this->db->from('test');
    $sub = $this->subquery->start_subquery('join', 'left', 't.ID=test.ID');
    $sub->select('ID, b')->from('test2')->where('date >', '2011-01-01');
    $this->subquery->end_subquery('t');
    $this->db->where('test.field', 4);

**UNION ALL**  
*SQL*:

    SELECT 1 AS A
    UNION ALL
    SELECT 2 AS A
    UNION ALL
    SELECT 3 AS A

*Active Record*:

    $sub1 = $this->subquery->start_union();
    $sub1->select('1 AS A', false);
    $sub2 = $this->subquery->start_union();
    $sub2->select('2 AS A', false);
    $sub3 = $this->subquery->start_union();
    $sub3->select('3 AS A', false);
    $this->subquery->end_union();

  [1]: http://stackoverflow.com/questions/4155873/mysql-find-in-set-vs-in/4156063#4156063
