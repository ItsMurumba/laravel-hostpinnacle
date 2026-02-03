<?php

namespace Itsmurumba\Hostpinnacle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Itsmurumba\Hostpinnacle\HostpinnacleCredentials;

class HostpinnacleAccount extends Model
{
    protected $table;

    protected $fillable = [
        'api_key',
        'sender_id',
        'username',
        'password',
        'base_url',
        'name',
    ];

    protected $casts = [
        'password' => 'encrypted',
    ];

    protected $hidden = [
        'password',
    ];

    public function __construct(array $attributes = [])
    {
        $this->table = config('hostpinnacle.saas.table', 'hostpinnacle_accounts');
        parent::__construct($attributes);
    }

    /**
     * Owner of this account (e.g. User). Configure owner_model and owner_key in config.
     */
    public function owner(): BelongsTo
    {
        $key = config('hostpinnacle.saas.owner_key', 'user_id');
        $model = config('hostpinnacle.saas.owner_model', 'App\\Models\\User');

        return $this->belongsTo($model, $key);
    }

    /**
     * Convert this account to credentials for use with Hostpinnacle.
     */
    public function toCredentials(): HostpinnacleCredentials
    {
        return HostpinnacleCredentials::fromArray([
            'api_key' => $this->api_key,
            'sender_id' => $this->sender_id,
            'username' => $this->username,
            'password' => $this->password,
            'base_url' => $this->base_url,
        ]);
    }
}
