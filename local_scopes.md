## Local Scopes

Local scopes allow you to define common sets of query constraints that you may easily re-use throughout your application. For example, you may need to frequently retrieve all users that are considered "popular". To define a scope, add the Scope attribute to an Eloquent method.

Scopes should always return the same query builder instance or void:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * Scope a query to only include popular users.
     */
    #[Scope]
    protected function popular(Builder $query): void
    {
        $query->where('votes', '>', 100);
    }

    /**
     * Scope a query to only include active users.
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('active', 1);
    }
}
```

Utilizing a Local Scope
Once the scope has been defined, you may call the scope methods when querying the model. You can even chain calls to various scopes:

```php
use App\Models\User;

$users = User::popular()->active()->orderBy('created_at')->get();
```

Combining multiple Eloquent model scopes via an or query operator may require the use of closures to achieve the correct logical grouping:

```php
$users = User::popular()->orWhere(function (Builder $query) {
    $query->active();
})->get();
```

However, since this can be cumbersome, Laravel provides a "higher order" orWhere method that allows you to fluently chain scopes together without the use of closures:

```php
$users = User::popular()->orWhere->active()->get();
```

## Dynamic Scopes

Sometimes you may wish to define a scope that accepts parameters. To get started, just add your additional parameters to your scope method's signature. Scope parameters should be defined after the $query parameter:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * Scope a query to only include users of a given type.
     */
    #[Scope]
    protected function ofType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }
}
```

Once the expected arguments have been added to your scope method's signature, you may pass the arguments when calling the scope:

```php
$users = User::ofType('admin')->get();
```

## Pending Attributes

If you would like to use scopes to create models that have the same attributes as those used to constrain the scope, you may use the withAttributes method when building the scope query:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * Scope the query to only include drafts.
     */
    #[Scope]
    protected function draft(Builder $query): void
    {
        $query->withAttributes([
            'hidden' => true,
        ]);
    }
}
```

The withAttributes method will add where conditions to the query using the given attributes, and it will also add the given attributes to any models created via the scope:

```php
$draft = Post::draft()->create(['title' => 'In Progress']);

$draft->hidden; // true
```

To instruct the withAttributes method to not add where conditions to the query, you may set the asConditions argument to false:

```php
$query->withAttributes([
    'hidden' => true,
], asConditions: false);
```
