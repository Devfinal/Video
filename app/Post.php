<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Request;

class Post extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'title',
        'content'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->belongsToMany(Comment::class);
    }

    public function media()
    {
        $media = $this->belongsToMany(Media::class);

        if (!is_null($mediaOrder = $this->getMeta('mediaOrder'))) {
            return $media->orderByRaw('field(id, ' . implode(',', $mediaOrder) . ')');
        }

        return $media;
    }

    public function meta()
    {
        return $this->belongsToMany(Meta::class);
    }

    /**
     * Returns the user associated with this post.
     *
     * @return User
     */
    public function getUser()
    {
        return User::find($this->getMeta('user')) ?: $this->user;
    }

    /**
     * Returns a single meta value associated with this post.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getMeta($key, $default = null)
    {
        return $this->meta->contains('key', $key) ? $this->meta->where('key', $key)->first()->value : $default;
    }

    /**
     * Sets a single meta value associated with this post.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setMeta($key, $value)
    {
        if ($this->meta->contains('key', $key)) {
            $meta = $this->meta->where('key', $key)->first();
            $meta->value = $value;
            $meta->save();
        } else {
            $this->meta()->create([
                'key' => $key,
                'value' => $value
            ]);
        }
    }

    /**
     * Deletes a single meta value associated with this post.
     *
     * @param string $key
     * @return void
     */
    public function deleteMeta($key)
    {
        $meta = $this->meta->where('key', $key)->first();

        if (!is_null($meta)) {
            $this->meta()->detach($meta->id);
        }
    }

    /**
     * Returns true if this post has protected media associated with it.
     *
     * @return bool
     */
    public function hasProtectedMedia()
    {
        foreach ($this->media as $media) {
            if ($media->protected) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if this post has video media associated with it.
     *
     * @return bool
     */
    public function hasVideo()
    {
        foreach ($this->media as $media) {
            if ($media->type === 'video') {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the total number of likes.
     *
     * @return int
     */
    public function getLikesCount()
    {
        return $this->likes_count ?:
            $this->userLikes->count() +
            $this->anonymousLikes()->count() +
            $this->getMeta('admin_likes', 0);
    }

    public function userLikes()
    {
        return $this->belongsToMany(User::class, 'like_post');
    }

    /**
     * Returns the anonymous likes associated with this post.
     *
     * @return \Illuminate\Support\Collection
     */
    public function anonymousLikes()
    {
        return DB::table('anonymous_like_post')
            ->where('post_id', $this->id)
            ->pluck('visitor');
    }

    /**
     * Associates an anonymous like with this post.
     *
     * @param string $ip
     * @return void
     */
    public function addAnonymousLike($ip)
    {
        DB::table('anonymous_like_post')
            ->insert([
                'post_id' => $this->id,
                'visitor' => $ip
            ]);
    }

    /**
     * Returns true if the given user likes this post.
     *
     * @param User|null $user
     * @return bool
     */
    public function doesUserLike(User $user = null)
    {
        if (is_null($user)) {
            return $this->anonymousLikes()->contains(Request::ip());
        }

        return $user->hasRole('admin') ?
            $this->getMeta('admin_likes', 0) > 0 :
            $this->userLikes->contains($user);
    }

    public static function boot()
    {
        parent::boot();

        // cleanup associated content
        Post::deleting(function (Post $post) {
            $post->media->each(function (Media $media) {
                $media->delete();
            });

            $post->meta->each(function (Meta $meta) {
                $meta->delete();
            });
        });
    }

    /**
     * Orders the posts with the most popular by likes first.
     *
     * @param Builder $builder
     * @return Builder
     */
    public function scopePopular(Builder $builder)
    {
        if (is_null($builder->getQuery()->columns)) {
            $builder->addSelect('*');
        }

        return $builder
            ->addSelect(DB::raw('IFNULL(user_likes.count, 0) AS user_likes'))
            ->addSelect(DB::raw('IFNULL(anonymous_likes.count, 0) AS anonymous_likes'))
            ->addSelect(DB::raw('CAST(IFNULL(admin_likes.count, 0) AS UNSIGNED) AS admin_likes'))
            ->addSelect(DB::raw('
                IFNULL(user_likes.count, 0) +
                IFNULL(anonymous_likes.count, 0) +
                IFNULL(admin_likes.count, 0) AS likes_count
            '))
            ->leftJoin(DB::raw('(
                SELECT
                    post_id,
                    COUNT(*) AS count
                FROM like_post
                GROUP BY post_id
            ) user_likes'), 'user_likes.post_id', '=', 'posts.id')
            ->leftJoin(DB::raw('(
                SELECT
                    post_id,
                    COUNT(*) AS count
                FROM anonymous_like_post
                GROUP BY post_id
            ) anonymous_likes'), 'anonymous_likes.post_id', '=', 'posts.id')
            ->leftJoin(DB::raw('(
                SELECT
                    posts.id as post_id,
                    SUBSTRING(
                        meta.value,
                        LOCATE(":", meta.value) + 1,
                        LOCATE(";", meta.value, (LOCATE(":", meta.value) + 1)) - LOCATE(":", meta.value) - 1
                    ) AS count
                FROM posts
                JOIN meta_post ON meta_post.post_id = posts.id
                JOIN meta ON meta.id = meta_post.meta_id
                WHERE meta.key = "admin_likes"
            ) admin_likes'), 'admin_likes.post_id', '=', 'posts.id');
    }
}
