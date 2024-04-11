## Usage

### Creating new extension:

1) Go to `Root DIR/src/app/`

   * Create Folder `Xxxxx`
     * Controller: Put your controller codes here
     * Model: Put you model codes here
     * view: Put you views here
     * routes.php: put your routes here
2) Routes
   Available methods: POST, GET, PATCH, DELETE

   Note: Ofcourse you can always add it depends on what you need. Please go to `Root DIR/src/base/Router/Route.php`

   sample usage:

```
<?php

use Base\Router\Route;
use App\User\Controller\User;

$user = new User();

// CRUD OPERATION
Route::post('/api/user', function ($req) use ($user) {
    return $user->createUser($req);
});

Route::get('/api/user/', function ($req) use ($user) {
    return $user->getUser($req);
});

Route::get('/api/user/:id', function ($req) use ($user) {
    return $user->getUser($req);
});

Route::patch('/api/user/:id', function ($req) use ($user) {
    return $user->updateUser($req);
});
```

### Setting Environment Varaibles

1. Go to `Root DIR/.env`

   You can access it like this `$_ENV['DB_HOST']`

   Please check vlucas/phpdotenv repository for more information

### Data validation

Currently was using the validation from `illuminate/validation`. Please see their documentations.

### Model

Currently was using PDO+mysql.

Usage

```
use Base\Model\Db;

Db::getInstance()->getConn();
```



### Public folder

1. Go to `Root DIR/public`

   Add your public files here like css, images, etc
