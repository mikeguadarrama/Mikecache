# Mikecache
Cache library for Query Builder in CodeIgniter 3.

Note: it will most likely not work with CodeIgniter 2.X.X because of *get_compiled_select()* missing.

# Usage
Add the library file to your application/library directory in CodeIgniter.
Then, load the library from your Controller.

```php
$this->load->library('mikecache');
// your QueryBuilder code goes here...
$data = $this->mikecache->cache($time_in_minutes, $optional_cache_dir_name);
```
Example:
```php
function my_method(){
	$this->load->library('mikecache');
	// write your query builder / active record
	$this->db->select('*');
	$this->db->from('my_table');
	// and then replace get() with the library:
	$data['results'] = $this->mikecache->cache(15, 'my_query');
	// this will cache the results with 15 minute TTL in a subdirectory
	// "my_query" inside the default cache directory (normally application/cache/mikecache/).
	$this->load->view('index', $data);
}
```
Deleting cache files
```php
// delete your caches
// will delete ALL your caches
$this->mikecache->clear(); 
// will only delete the cache inside "my_query" subdirectory
$this->mikecache->clear('my_query');
```

# What it does
The library picks up the query where you left off. First it checks if it has been cached and compares the cached filetime with your supplied time. If file is recent, then it loads the serialized cached results from file and sends it back. If not, it just finishes the query using query builder, caches the result and sends it back to your controller.

# When is it useful?
This library is useful if your queries are large, complicated and/or repetitive.

Example:
```php
// Good example
$this->db->select('*');
$this->db->from('my_table t');
$this->db->join('other_table t2', 't2.id = t.id_table2');
$this->db->where('date(created)', 'DATE(Now())', FALSE);
$this->db->order_by('created', 'DESC')->limit(10);
```
This is a good example because your query will be exactly the same everytime you run it (at least for a day), then the library will help you by reducing database access and query times, especially if it's a large and complicated query.

# When is it not useful?
It only works for SELECT queries.
```php
// The library will not help you much in some cases
$this->db->select('*');
$this->db->from('my_table t');
$this->db->join('other_table t2', 't2.id = t.id_table2');
$this->db->where('created <', '2015-04-20 15:23:03', FALSE);
$this->db->order_by('created', 'DESC')->limit(10);
```
This is bad use for the library because everytime you run the query it will be different, so a new cache will be made and the previous ones will remain forever until you clear them.
This library relies on the fact that queries will be the same or at least not changing by the second (Eg: explicit datetime values).

Also, when there are many different queries running, like, "where article_id = $id ", you might fill your disk with cache files, so keep that in mind for large websites with variable queries.

# Some History
I had some queries that according to CodeIgniter Profiler were taking over 4 seconds to return results, like getting the latests articles (over 100K rows) ordering by hits/views. Mostly because of the ORDER BY clause. So I looked around for a library but couldn't find one that would suit my needs.
This library saves me tons of database access and processing time.

# About Me
Web developer, CodeIgniter lover
Venezuela
