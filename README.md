# DBHelper For PHP

**You can do many database operations without writing any SQL code with DBHelper. you need complex SQL queries or you want write sql query? DBHelper allows you to do this too.**

## Example Usage

**PHP**
```php
include "DBHelper/DBHelper.php";
use \DBHelper\DBHelper;

$db = new DBHelper();

$db->connect();

$users = $db->get('users');
print_r($users);

$db->disconnect();
```

**SQL Print**

```sql
SELECT * FROM users
```
**Result**
```
(
    [0] => Array (
        [user_id] => 1
        [user_mail] => foobar@github.com
        [user_password] => 12345
        [json] => {"user":"Foo Bar","messages":["Foo","Bar"]}
        [created_at] => 2021-05-09 02:57:49
    )
    ...
)
```
## Table of Contents
  * [Config](#config)
  * [Connect](#connect)
  * [Disconnect](#disconnect)
  * [Methods](#methods)
      * [Get All](#-get-all)
      * [Get First](#-get-first)
      * [Update](#-update)
      * [Delete](#-delete)
      * [Query Builder](#-query-builder)
  * [Params](#params)
      * [whereIn](#-wherein)
      * [whereOr](#-whereor)
      * [whereLike](#-wherelike)
      * [whereNotIn](#-wherenotin)
      * [whereNotOr](#-wherenotor)
      * [whereNotLike](#-wherenotlike)
      * [fields](#-fields)
      * [distinct](#-distinct)
      * [count](#-count)
      * [orderBy](#-orderby)
      * [limit](#-limit)
      * [offset](#-offset)
      * [jsonParser](#-jsonparser)
      * [values](#-values)

## Config
You can configuration for database settings from `DBHelper/DBHelper.php`

```php
private $host = '';
private $user = '';
private $password = '';
private $database = '';
```

Next, include DBHelper where you want to use it and create DBHelper.

```php
include "DBHelper/DBHelper.php";
use \DBHelper\DBHelper;

$db = new DBHelper();
```


## Connect
If you want access from database, you need connect to database.

```php
$db->connect();
```

## Disconnect
You can disconnect database when your work is done.

```php
$db->disconnect();
```

## Methods

### # Get All
###### **PHP**
```php
$db->get('users');
```

###### **SQL**
```sql
SELECT * from users
```

### # Get First
###### **PHP**
```php
$db->first('users');
```

###### **SQL**
```mysql
SELECT * from users LIMIT 1
```

### # Insert
###### **PHP**
```php
$values = array(
    'user_mail' => 'foo@bar.com',
    'user_password' => 'foobar'
);
$db->values($values);
$db->insert('users');
```

###### **SQL**
```mysql
INSERT INTO users SET user_mail = "foo@bar.com", user_password = "foobar"
```

### # Update
###### **PHP**
```php
$values = array(
    'user_mail' => 'bar@foo.com'
);
$db->values($values);

$db->whereIn(array(
    'user_mail' => 'foo@bar.com'
));
$db->update('users');
```

###### **SQL**
```mysql
UPDATE users SET user_mail = "bar@foo.com" WHERE user_mail = "foo@bar.com"
```

### # Delete
###### **PHP**
```php
$db->whereIn(array(
    'user_mail' => 'foo@bar.com'
));
$db->delete('users');
```

###### **SQL**
```mysql
DELETE FROM users WHERE user_mail = "foo@bar.com"
```

### # Query Builder
###### **PHP**
```php
$db->queryBuilder('SELECT * FROM users');
```

###### **SQL**
```mysql
SELECT * FROM users
```

## Params

### # whereIn
###### **PHP**
```php
$db->whereIn(array(
    'user_mail' => 'foo@bar.com',
    'user_passowrd' => 'foobar'
));
```

###### **SQL**
```mysql
WHERE user_mail = "foo@bar.com" AND user_password = "foobar"
```

### # whereOr
###### **PHP**
```php
$db->whereIn(array(
    'user_mail' => 'foo@bar.com',
    'user_passowrd' => 'foobar'
));
```

###### **SQL**
```mysql
WHERE user_mail = "foo@bar.com" OR user_password = "foobar"
```

### # whereLike
###### **PHP**
```php
$db->whereLike(array(
    'user_mail' => 'foo@bar.com',
    'user_passowrd' => 'foobar'
));
```

###### **SQL**
```mysql
WHERE user_mail LIKE "foo@bar.com" AND user_password LIKE "foobar"
```

### # whereNotIn
###### **PHP**
```php
$db->whereNotIn(array(
    'user_mail' => 'foo@bar.com',
    'user_passowrd' => 'foobar'
));
```

###### **SQL**
```mysql
WHERE user_mail != "foo@bar.com" AND user_password != "foobar"
```

### # whereNotOr
###### **PHP**
```php
$db->whereNotOr(array(
    'user_mail' => 'foo@bar.com',
    'user_passowrd' => 'foobar'
));
```

###### **SQL**
```mysql
WHERE user_mail != "foo@bar.com" OR user_password != "foobar"
```

### # whereNotLike
###### **PHP**
```php
$db->whereNotLike(array(
    'user_mail' => 'foo@bar.com',
    'user_passowrd' => 'foobar'
));
```

###### **SQL**
```mysql
WHERE user_mail NOT LIKE "foo@bar.com" AND user_password NOT LIKE "foobar"
```

### # fields
###### **PHP**
```php
$db->fields(array(
    'user_mail',
    'user_passowrd'
));
```

###### **SQL**
```mysql
SELECT user_mail, user_password
```

### # distinct
###### **PHP**
```php
$db->fields(array(
    'user_mail'
));
$db->distinct(true);
```

###### **SQL**
```mysql
SELECT DISTINCT user_mail
```

### # count
###### **PHP**
```php
$db->fields(array(
    'user_mail'
));
$db->count(true);
```

###### **SQL**
```mysql
SELECT COUNT(user_mail)
```

### # orderBy
###### **PHP**
```php
$db->orderBy('created_at', 'ASC');
```

###### **SQL**
```mysql
ORDER BY created_at ASC
```

### # limit
###### **PHP**
```php
$db->limit(1);
```

###### **SQL**
```mysql
LIMIT 1
```

### # offset
###### **PHP**
```php
$db->offset(15);
```

###### **SQL**
```mysql
OFFSET 15
```

### # jsonParser
If you saving JSON on database, you can parse array to json or json to array with `jsonParser`. Just say pattern of your json data to `jsonParser`
###### **PHP**
```php
$json = array(
    'foo' => array(
        'bar'
    )
);
$values = array(
    'json' => $json
);

$db->values($values);

$db->whereIn(array(
    'user_mail' => 'foo@bar.com'
));

$db->jsonParser(array(
    'json'
));

$db->update('users');
```

###### **SQL**
```mysql
UPDATE users SET json = "{\"foo\":[\"bar\"]}" WHERE user_mail = "foo@bar.com"
```

### # values
###### **PHP**
```php
$values = array(
    'user_mail' => 'foo@bar.com',
    'user_password' => 'foobar'
);
$db->values($values);

$db->insert('users');
```

###### **SQL**
```mysql
INSERT INTO users SET user_mail = "foo@bar.com", user_password = "foobar"
```
