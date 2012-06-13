PHP ADVANCED PAGINATION
========================

Basic Usage:
```
$PG=new _Pagination();

$DATA=$PG->paginazione($number_lines_per_page, $sql_query_no_limit, $_number_of_links_before_and_after_selected_page);
echo $SQL[0];
while ($NW=mysql_fetch_assoc($SQL[1]))
{
	echo $NW['descrizione_ita'].'<hr>';
}
echo $SQL[2];
echo $SQL[3];
```
