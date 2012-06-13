PHP ADVANCED PAGINATION
========================
Demo: [BASIC DEMO](http://www.smartgap.it/repos/phpAdvancedPagination/basic.php) \n

Demo: [SET ARROWS](http://www.smartgap.it/repos/phpAdvancedPagination/set_arrows.php)

Basic Usage:
```
$PG=new _Pagination();

$DATA=$PG->paginazione($number_lines_per_page, $sql_query_no_limit, $_number_of_links_before_and_after_selected_page);
echo $DATA[0];
while ($NW=mysql_fetch_assoc($DATA[1]))
{
	echo $NW['descrizione_ita'].'<hr>';
}
echo $DATA[2];
echo $DATA[3];
```

Set Arrows:
```
...
$PG->set_arrows($next, $prev, $separator, $fullsteps);
$DATA=$PG->paginazione($number_lines_per_page, $sql_query_no_limit, $_number_of_links_before_and_after_selected_page);
...
```
