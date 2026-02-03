<?php

namespace Itsmurumba\Hostpinnacle\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Itsmurumba\Hostpinnacle\HostpinnacleCredentials;

/**
 * Stored Hostpinnacle account (credentials) for SaaS multi-account usage.
 */
class HostpinnacleAccount extends Model
{
    /** @var string|null */
    protected $table;

    /** @var array<int, string> */
    protected $fillable = [
        'api_key',
        'sender_id',
        'username',
        'password',
        'base_url',
        'name',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'password' => 'encrypted',
    ];

    /** @var array<int, string> */
    protected $hidden = [
        'password',
    ];

    /**
     * Table name is read from config (hostpinnacle.saas.table).
     *
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('hostpinnacle.saas.table', 'hostpinnacle_accounts');
        parent::__construct($attributes);
    }

    /**
     * Owner of this account (e.g. User). Configure owner_model and owner_key in config.
     *
     * @return BelongsTo
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
