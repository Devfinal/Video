<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'price',
        'items'
    ];

    public function media()
    {
        return $this->belongsToMany(Media::class);
    }

    public function meta()
    {
        return $this->belongsToMany(Meta::class);
    }

    /**
     * Returns a single meta value associated with this product.
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
     * Sets a single meta value associated with this product.
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
     * Deletes a single meta value associated with this product.
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
     * Returns true if this product is available in stock.
     *
     * @return bool
     */
    public function inStock()
    {
        return $this->items > 0;
    }

    /**
     * Adds the given number of products to stock.
     *
     * @param int $count
     */
    public function addItems($count)
    {
        $this->update([
            'items' => $this->items + $count
        ]);
    }

    /**
     * Subtracts the given number of products from stock.
     *
     * @param int $count
     */
    public function subtractItems($count)
    {
        $this->update([
            'items' => $this->items - $count
        ]);
    }
}
