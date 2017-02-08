<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'meta';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value'
    ];

    /**
     * Accessor for the value attribute.
     *
     * @param string $value
     * @return mixed
     */
    public function getValueAttribute($value)
    {
        return unserialize($value);
    }

    /**
     * Mutator for the value attribute.
     *
     * @param mixed $value
     * @return void
     */
    public function setValueAttribute($value)
    {
        $this->attributes['value'] = serialize($value);
    }

    public function background()
    {
        return $this->belongsTo(Media::class);
    }

}
