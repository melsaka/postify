<?php

namespace Melsaka\Postify\Models;

use Melsaka\Postify\Features\Searchable;
use Illuminate\Database\Eloquent\Model;
use Melsaka\Commentable\HasComments;
use Melsaka\Categorist\Categorized;
use Melsaka\MediaFile\Models\Media;
use Melsaka\Metable\Metable;
use App\Models\User;

class Post extends Model
{
    use HasComments;
    use Categorized;
    use Searchable;
    use Metable;

    // Settings

    protected $fillable = [
        'slug',
        'title',
        'content',
        'description',
        'excerpt',
        'published',
        'published_at',
        'comments_status',
        'media_id',
        'user_id',
    ];

    protected $searchable = [
        'columns' => [
            'posts.title' => 10,
            'posts.description' => 2,
        ],
    ];

    /**
     * The attributes that should be casted.
     *
     * @var array
     */
    protected $casts = [
        'published' => 'boolean',
        'comments_status' => 'boolean'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    public $dates = [
        'published_at',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Relationships

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    // Helpers

    /**
     * Get first category in the post categories.
     *
     * @return \Melsaka\Categorist\Models\Category
     */
    public function firstCategory()
    {
        return $this->categories->first();
    }

    /**
     * Get all images used in post content.
     *
     * @return array
     */
    public function getContentImages()
    {
        $images = [];

        preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $this->content, $contentImages);

        foreach($contentImages[1] as $contentImage) {
            $baseUrl = url('/');

            $imageUrl = urldecode($contentImage);

            // If the image URL is a relative URL, prepend the base URL
            if (strpos($imageUrl, '../') === 0) {
                $imageUrl = $baseUrl .'/'. preg_replace('/^(\.\.\/)+/', '', $imageUrl);
            }

            $images[] = $imageUrl;
        }

        return $images;
    }

    /**
     * Get post image link.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->media->getEncodedImageLink();
    }

    /**
     * Build post image srcset attribute and return it.
     *
     * @return string
     */
    public function getSrcset()
    {
        return $this->media->getSrcset();
    }

    /**
     * Get publish date in human readable format.
     *
     * @return string
     */
    public function publishedAt()
    {
        return $this->published_at->diffForHumans();
    }

    /**
     * Scope a query to only include published posts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    /**
     * Scope a query to only include drafts (unpublished posts).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDraft($query)
    {
        return $query->where('published', false);
    }

    /**
     * Scope a query to only include posts whose publish date is in the past (or now).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLive($query)
    {
        return $query->published()->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include posts whose publish date is in the future.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeScheduled($query)
    {
        return $query->where('published_at', '>', now());
    }

    /**
     * Scope a query to only include posts whose publish date is before a given date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBeforePublishDate($query, $date)
    {
        return $query->where('published_at', '<=', $date);
    }

    /**
     * Scope a query to only include posts whose publish date is after a given date.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAfterPublishDate($query, $date)
    {
        return $query->where('published_at', '>', $date);
    }
}
