# Single-Trait Cloner for Eloquent Models and their Nested Relations
## What is the purpose of this project?
This repository is a practical project, not a standalone package. It showcases a single trait that can be easily cloned
or copied for efficient cloning of Eloquent models and their nested relations. Crafted for seamless integration, it
saves time whenever complex model cloning is needed in your project.
For simplicity and clarity, this implementation avoids specific design patterns or principles. The primary focus is on
the straightforward cloning of models and their nested relations.


## The idea behind the project
In response to a task involving the cloning of models and their nested relations in another project, I have created a
trait that encapsulates the specific requirements of the task, with the intention of sharing it with the Laravel
community. While the entire trait is designed to be universally applicable to any Laravel project, it is worth noting
that one of the methods is tailored to address specific needs within my project. This method is detailed in the
"Special Requirement" section below. You have the flexibility to easily adjust or omit this method based on your
project's unique requirements.
The remaining functionality in the trait is generic and can seamlessly integrate into any Laravel project, offering a
solution for cloning models and their nested relationships, see the code and tests for more details.


## About the project
This trait offers a convenient method to clone Eloquent model instances along with their (nested) relations.
It draws inspiration from and is adapted from the [BKWLD/cloner](https://github.com/BKWLD/cloner/tree/master) package,
developed by [Robert Reinhard](https://github.com/weotch). The repository aims to provide a minimalistic, reusable, and
easy-to-use solution for projects with specific requirements, especially those dealing with nested relationships. If
your project involves a unique relationship pattern, such as the "Author - Post - Review" structure (see Special
Requirement section below), this trait might be the lightweight solution you need. For more complex scenarios, including
the cloning of relations and associated files, consider using the original
[BKWLD/cloner](https://github.com/BKWLD/cloner/tree/master) package.

Ensure to review the constraints outlined at the end of this document before implementing the trait.
Additionally, the `duplicatePivotedRelation` method includes comments explaining special considerations during
many-to-many cloning.


## How to use
### Acknowledgement
To use the code from this trait, please reference the original
[BKWLD/cloner](https://github.com/BKWLD/cloner/tree/master) package, and [Robert Reinhard](https://github.com/weotch)
for developing it. This trait and the [BKWLD/cloner](https://github.com/BKWLD/cloner/tree/master) are different but the
idea is the same. The trait is a simplified version of the package (for example, no file cloning), with some additional
features, such as the Special Requirement outlined below.


### Installation
Define the relations you want to clone using the $cloneableRelations property in your models, specify columns to
exclude during cloning with $exceptColumns. Do not forget that the order of relations in $cloneableRelations determines
the actual cloning sequence. Your model should look like this:

```
class Author extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    public array $exceptColumns = ['id', 'created_at', 'updated_at', 'deleted_at'];
    public array $cloneableRelations = ['posts', 'reviews'];
```


### Usage
Implement the trait in classes where cloning is needed and use the duplicate method to clone relations,
adhering to the order specified in $cloneableRelations. For example,

```
class AuthorController extends Controller
{
    use ModelClonerTrait;

    public function clone(int $id): JsonResponse
    {
        $author = Author::findOrFail($id);

        try {
            $clone = $this->duplicate($author);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);

        }

        $clone = $clone->load([
            'posts' => function ($query) {
                $query->with([
                    'image',
                    'comments.tags.tagType'
                ]);
            },
            'reviews',
        ]);

        return response()->json(['data' => $clone], 201);
    }
```

The 'replicate' method is a Laravel method from Illuminate\Database\Eloquent\Model, and it is used to clone the model
itself. It has a number of methods and some of them are used in this trait.  
The 'duplicate' method is used to clone the model and its relations. The 'duplicate' method accepts an optional array of
relations to clone. If no relations are specified, the method will not clone any of the relations the model has.


## Important notes
$cloneableRelations needs to be an array of strings. The strings represent the names of the relation methods in your
models. For example,

```
class Author extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    public array $exceptColumns = ['id', 'created_at', 'updated_at', 'deleted_at'];
    public array $cloneableRelations = ['posts', 'reviews'];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
```

## Cloned models and relations in this project

- **Author**
- **Post**
- **Review**
- **Image**
- **Comment**
- **Tag**
- **TagType**

The relationships are as follows:
Author - Post (many-to-many relation)
Author - Review (one-to-many relation)

Post - Review (one-to-many relation)
Post - Image (polymorphic one-to-one relation)
Post - Comment (one-to-many relation)
Comment - Tag (one-to-many relation)
Tag - TagType (many-to-one relation)

### Special requirement
Both Author and Post are related with the Review whose table has author_id and post_id. As according to my project
requirements, the Post related to the author is cloned after the Review, the cloned Post IDs have to be updated in
the Review table.
This is done in `updateReviews` method. The cloned Post IDs are stored in the $clonedIds, that is, the $clonedIds
property is an array of arrays, where the key is the original ID and the value is the cloned ID, see the method for more
details. Bear in mind that, if the order of cloning requirements were different, the methods would have to be adjusted
accordingly.
In addition, if the cloned model is Author, the name should have the suffix ' (Cloned)'.


## Database seeding
The database seeding can be done with the following command:

```
php artisan db:seed
```

The seeded data will be sufficient to perform any cloning via two routes (use any testing tool):
- http://localhost:8000/api/authors/1/clone
- http://localhost:8000/api/posts/1/clone

Bear in mind that the seeded data will be deleted after each test, see Testing section below. Therefore, you will need
to seed the database again before performing any cloning via API tools.


## Testing
The cloning process is tested in the CloneTest, testCloneAuthorAndRelatedModels and testClonePostAndRelatedModels
methods. The trait DatabaseMigrations is used to refresh the database before each test.
The test database is seeded with the data located in the test trait CloneTestData. It also contains the expected
response, that is, the structure of the data and actual data that is expected to be returned after cloning the models.

To run the tests, follow these steps:
- Clone the repository,
- Install dependencies,
- Connect to the database,
- Run the tests.

```
php artisan test
```


## Constraints
Parent and child models that are related via hasMany with the same model cannot both clone that model, only one of them
can. Otherwise, duplicate data might be created. For example, if an Author has many Post and vice-versa, and they both
have many Review, then only one of them can clone the Review. Otherwise, duplicate Review records will be created.

Parent and child that are related via pivot (many-to-many) cannot clone each other as an infinite loop will occur.

There might be more constraints, but I did not perform detailed testing outside the scope of my project.
Feel free to copy any of the code from this project and play with it, just reference the
[BKWLD/cloner](https://github.com/BKWLD/cloner/tree/master) and [Robert Reinhard](https://github.com/weotch), if you are
planning to use it for commercial purposes.
