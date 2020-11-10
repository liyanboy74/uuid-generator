# UUID Generator

PHP UUID Generate and save to MySQL database.

you must have table named `uuid` Columns (`ID` ,`name` ,`uuid`)

Edit below lines in `guuid.php` for connect to MySQL database:

```php
$servername     = 	"localhost"	;
$username 	= 	"USERNAME"	;
$password 	= 	"PASSWORD"	;
$dbname 	= 	"DB_NAME"	;
```

for redirect `HTTP` to `HTTPS` use `.htaccess` file , else remove that.