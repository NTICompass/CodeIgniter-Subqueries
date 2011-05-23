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

**join_range**: Helper function to CROSS JOIN a list of numbers (From [this][1] StackOverflow answer)
*Parameters*:

 - $start: Range start
 - $end: Range end
 - $alias: Alias for number list
 - $table_name: JOINed tables need an alias (Optional)

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
