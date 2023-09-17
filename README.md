# Postify for Laravel

Postify is a versatile Laravel package designed to simplify the process of creating and managing posts in your Laravel applications. It comes packed with features such as meta data, categories, images, and comments, making it a powerful tool for content-driven websites and blogs.

## Installation

Add the package to your Laravel app via Composer:

```bash
composer require melsaka/postify
```

Register the package's service provider in config/app.php.

```php
'providers' => [
    ...
    Melsaka\Postify\PostifyServiceProvider::class,
    ...
];
```

Run the migrations to add the required table to your database:

```bash
php artisan migrate
```

Add `CanComment` trait to the User Model:

```php
use Melsaka\Commentable\CanComment;

class User extends Model
{
    use CanComment;
    
    // ...   
}
```

## Configuration

To configure the package, publish its configuration file:

```bash
php artisan vendor:publish --tag=postify
```

You can then modify the configuration file to change the posts table name if you want, default: `posts`.

## Usage

### Creating Posts

Postify provides an easy way to `create` and **manage posts** in your Laravel application. Here's an example of how to create a **new post**:

```php
use Melsaka\MediaFile\MediaFolder;
use Melsaka\MediaFile\MediaFile;
use Melsaka\Postify\Models\Post;

$folder = (new MediaFolder())->store('posts-images', 1);

$media = (new MediaFile($folder))->store($uploadedFile);

$post = Post::create([
    'slug'          => 'post-slug', // unique string 
    'title'         => 'post title', // string
    'content'       => 'Post Content', // text
    'description'   => 'post description', // string
    'excerpt'       => 'post excrept', //string
    'published'     => true, // boolean
    'media_id'      => $media->id, // integer
    'user_id'       => auth()->id(), // integer
]);
```

### Managing Categories

Postify integrates with the [**Categorist**](https://github.com/melsaka/categorist) package, allowing you to categorize your posts. You can assign `categories` to a `post` like this:

```php
// Attach categories to a model
$post->attachCategories($category);

// Detach categories from a model
$post->detachCategories($category);

// Sync categories with a model
$post->syncCategories($category);

// Get post categories
$post->categories;
```

For more checkout [**Categorist**](https://github.com/melsaka/categorist) package.

### Adding Comments

Postify integrates with the [**Commentable**](https://github.com/melsaka/commentable) package, enabling you to easily manage comments on your posts. To retrieve `comments` for a `post`:

```php
// Add comment to post
$post->addComment('new comment', $user);

// Get post comments
$post->comments;
```

For more checkout [**Commentable**](https://github.com/melsaka/commentable) package.

### Handling Images

The package also includes image management features through the [**MediaFile**](https://github.com/melsaka/mediafile) package, and post meta data through the [**Metable**](https://github.com/melsaka/metable) package. 

For example You can get the post content `images` and store it as post metadata for seo purposes:

```php
use Melsaka\MediaFile\MediaFile;

// upload the image
$media = (new MediaFile($folder))->store($uploadedFile);

// update post image
$post->update(['media_id', $media->id]);

// Get array of post content images
$images = $post->getContentImages();

// store it as post metadata
$post->addMeta('contentImages', $post->getContentImages());
```

For more checkout [**MediaFile**](https://github.com/melsaka/mediafile) package, and [**Metable**](https://github.com/melsaka/metable) package.

### Post Model Methods

These are some of the available methods in `Post` Model that you can use:

```php
// Get the user who created this post
$post->author;

// Get the post image
$post->media

// Get first category from the post categories.
$post->firstCategory();

// Get all images used in post content.
$post->getContentImages();

// Get post original image link.
$post->getImage();

// Build post image srcset attribute and return it.
$post->getSrcset();

// Get publish date in human readable format.
$post->publishedAt();

// Scope a query to only include published posts.
Post::published();

// Scope a query to only include drafts (unpublished posts).
Post::draft();

// Scope a query to only include posts whose publish date is in the past (or now).
Post::live();

// Scope a query to only include posts whose publish date is in the future.
Post::scheduled();

// Scope a query to only include posts whose publish date is before a given date.
Post::beforePublishDate();

// Scope a query to only include posts whose publish date is after a given date.
Post::afterPublishDate();
``` 

## License

This package is released under the MIT license (MIT).
