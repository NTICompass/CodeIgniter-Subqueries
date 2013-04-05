Subqueries.  For CodeIgniter.
=============================
**By [NTICompass][1] (aka Rocket Hazmat)**


## Information ##
This is a subquery library for CodeIgniter’s (1.7.x - 2.0.2) active record class.  It lets you use the active record methods to create subqueries in your SQL.

It supports `SELECT`, `JOIN`, `FROM`, `WHERE`, etc. It also supports `UNION ALL`!

<sub>(Yes, you can have subqueries inside subqueries inside `UNION`s and `UNION`s inside subqueries.)</sub>

## Instructions ##
Put `Subquery.php` into `/application/libraries`, then load it in your code using `$this->load->library('subquery');`.
I guess you can add `'subquery'` to `$autoload['libraries']` (in `/application/config/autoload.php`), if you want.

### CodeIgniter 2.1.x ###
This library doesn't work with CodeIgniter 2.1.x out of the box.  It requires modifications to a file in `/system` to make it work.

You need to edit `/system/database/DB_active_rec.php` and modify the signature of `_compile_select` (should be line 1673).
In the older version(s) of CodeIgniter, this function was *not* `protected`, so if you remove the `protected` keyword from the function, my library will work.

<sub>(There's probably a reason this function is `protected`.)</sub>

In the `develop` version of CodeIgniter (which works with this library just fine, by the way), there is a `public` function that you can use.
You can "steal" the `get_compiled_select` function from the `/system/database/DB_query_builder.php` file (line 1283).

    /**
     * Get SELECT query string
     *
     * Compiles a SELECT query string and returns the sql.
     *
     * @param    string    the table name to select from (optional)
     * @param    bool    TRUE: resets QB values; FALSE: leave QB vaules alone
     * @return    string
     */
    public function get_compiled_select($table = '', $reset = TRUE)
    {
        if ($table !== '')
        {
            $this->_track_aliases($table);
            $this->from($table);
        }

        $select = $this->_compile_select();

        if ($reset === TRUE)
        {
            $this->_reset_select();
        }

        return $select;
    }

Put this function inside `/system/database/DB_active_rec.php`.

My library will check for the existance of either a `_compile_select` or `get_compiled_select` method.
If none of these methods exist, the library will fail to load.

## Methods ##

- `start_subquery`: Creates a new database object to be used for the subquery
  - Parameters:
      - `$statement`: SQL statement to put subquery into ('select', 'from', 'join', 'where', 'where_in', etc.)
      - `$join_type`: JOIN type (only for join statements)
      - `$join_on`: JOIN ON clause (only for join statements)
  - Returns: CodeIgniter db object to call active record methods on
- `end_subquery`: Closes the database object and writes the subquery
  - Parameters:
      - `$alias`: Alias to use in query, or field to use for WHERE
      - `$operator`: Operator to use for WHERE ('=', '!=', '<', etc.) / WHERE IN (TRUE for WHERE IN, FALSE for WHERE NOT IN)
          - If it's a SELECT, this parameter will turn it into `COALESCE((SELECT ...), $operator) AS $alias`
      - `$database`: Database object to use when dbStack is empty (optional)
  - Returns: Nothing
- `start_union`: Creates a new database object to be used for unions
  - Parameters: None
  - Returns: CodeIgniter db object to call active record methods on
- `end_union`: Combines all opened db objects into a `UNION ALL` query
  - Parameters:
      - `$database`: Database object to use when dbStack is empty (optional)
  - Returns: Nothing

## Examples ##

The most basic use of this library is to have a subquery in a `SELECT` statement.  This is very simple.
Let's say you want to get this query:

    SELECT field1, (SELECT field2 FROM table2 WHERE table1.field3 = table2.field3) as field2X
    FROM table1 WHERE field4 = 'test'

You would do this in your code:

    $this->db->select('field1');
    $sub = $this->subquery->start_subquery('select');
    $sub->select('field2')->from('table2');
    $sub->where('table1.field3 = table2.field3');
    $this->subquery->end_subquery('field2X');
    $this->db->from('table1')
    $this->db->where('field4', 'test');

If it's possible that your subquery might return a `NULL` row, you can set a default value.  That's done like this:

    $this->db->select('field1');
    $sub = $this->subquery->start_subquery('select');
    $sub->select('field2')->from('table2');
    $sub->where('table1.field3 = table2.field3');
    // Note the second parameter here
    $this->subquery->end_subquery('field2X', 'field5');
    $this->db->from('table1')
    $this->db->where('field4', 'test');

This will generate:

    SELECT field1, COALESCE((SELECT field2 FROM table2 WHERE table1.field3 = table2.field3), field5) as field2X
    FROM table1 WHERE field4 = 'test'


By passing different values to `start_subquery`, you can make this library do anyting!

Here's a `WHERE IN` example:

    $this->db->select('field1, field2')->from('table1');
    $sub = $this->subquery->start_subquery('where_in');
    $sub->select('field3')->from('table2')->where('field2', 'test');
    $this->subquery->end_subquery('field4', FALSE);

This will generate:

    SELECT field1, field2 FROM table1
    WHERE field4 NOT IN (SELECT field3 FROM table2 WHERE field2 = 'test')

`UNION` queries have a *slightly* different syntax.  For subqueries, every `start_subquery` needs an `end_subquery`,
but with `UNION` you only need one `end_union` - no matter how many `start_union`s you have.

    $sub1 = $this->subquery->start_union();
    $sub1->select('field1')->from('table1');
    $sub2 = $this->subquery->start_union();
    $sub2->select('field2')->from('table2');
    $sub3 = $this->subquery->start_union();
    $sub3->select('field3')->from('table3');
    $this->subquery->end_union();
    $this->db->order_by('field1', 'DESC');

  [1]: http://labs.nticompassinc.com
