<?php

namespace Itsmurumba\Hostpinnacle\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Itsmurumba\Hostpinnacle\Models\HostpinnacleAccount;

/** @mixin HostpinnacleAccount */
class HostpinnacleAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sender_id' => $this->sender_id,
            'username' => $this->username,
            'api_key' => $this->maskApiKey($this->api_key),
            'base_url' => $this->base_url,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    private function maskApiKey(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $len = strlen($value);
        if ($len <= 8) {
            return str_repeat('*', $len);
        }
        return substr($value, 0, 4) . str_repeat('*', $len - 8) . substr($value, -4);
    }
}
