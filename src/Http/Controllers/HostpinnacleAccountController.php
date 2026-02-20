<?php

namespace Itsmurumba\Hostpinnacle\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Itsmurumba\Hostpinnacle\Http\Resources\HostpinnacleAccountResource;
use Itsmurumba\Hostpinnacle\Models\HostpinnacleAccount;

/**
 * CRUD controller for Hostpinnacle accounts (SaaS). Responds with JSON for API/wantsJson, redirect+flash for web.
 */
class HostpinnacleAccountController extends Controller
{
    /**
     * Get the current authenticated owner ID (from config owner key).
     * Supports integer or string identifiers (e.g. UUID).
     *
     * @return int|string|null
     */
    protected function ownerId(): int|string|null
    {
        $user = Auth::user();
        if ($user === null) {
            abort(401, __('Authentication required.'));
        }
        return $user->getAuthIdentifier();
    }

    /**
     * Query scope: accounts belonging to the current owner.
     *
     * owner_key must match the column created by the migration (do not change saas.owner_key after migrating).
     *
     * @return \Illuminate\Database\Eloquent\Builder<HostpinnacleAccount>
     */
    protected function query(): \Illuminate\Database\Eloquent\Builder
    {
        $key = config('hostpinnacle.saas.owner_key', 'user_id');
        $ownerId = $this->ownerId();

        return HostpinnacleAccount::query()->where($key, $ownerId);
    }

    /**
     * List the current owner's Hostpinnacle accounts.
     *
     * @return JsonResponse|RedirectResponse
     */
    public function index(): JsonResponse|RedirectResponse
    {
        $accounts = $this->query()->get();

        if (request()->wantsJson()) {
            return response()->json([
                'data' => HostpinnacleAccountResource::collection($accounts),
            ]);
        }

        return redirect()->back()->with('hostpinnacle_accounts', $accounts);
    }

    /**
     * Show a single Hostpinnacle account (owner-only).
     *
     * @param  HostpinnacleAccount  $account
     * @return JsonResponse|RedirectResponse
     */
    public function show(HostpinnacleAccount $account): JsonResponse|RedirectResponse
    {
        $this->authorizeAccount($account);

        if (request()->wantsJson()) {
            return response()->json([
                'data' => new HostpinnacleAccountResource($account),
            ]);
        }

        return redirect()->back()->with('hostpinnacle_account', $account);
    }

    /**
     * Create a new Hostpinnacle account for the current owner.
     *
     * @return JsonResponse|RedirectResponse
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'api_key' => ['required', 'string', 'max:255'],
            'sender_id' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
            'base_url' => ['nullable', 'string', 'url', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $key = config('hostpinnacle.saas.owner_key', 'user_id');
        $validated[$key] = $this->ownerId();

        $account = HostpinnacleAccount::create($validated);

        if (request()->wantsJson()) {
            return response()->json([
                'data' => new HostpinnacleAccountResource($account),
                'message' => __('Hostpinnacle account created.'),
            ], 201);
        }

        return redirect()->back()->with('success', __('Hostpinnacle account created.'));
    }

    /**
     * Update a Hostpinnacle account (owner-only).
     *
     * @param  HostpinnacleAccount  $account
     * @return JsonResponse|RedirectResponse
     */
    public function update(Request $request, HostpinnacleAccount $account): JsonResponse|RedirectResponse
    {
        $this->authorizeAccount($account);

        $validated = $request->validate([
            'api_key' => ['sometimes', 'string', 'max:255'],
            'sender_id' => ['sometimes', 'string', 'max:50'],
            'username' => ['sometimes', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'base_url' => ['nullable', 'string', 'url', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        if (($validated['password'] ?? '') === '') {
            unset($validated['password']);
        }

        $account->update($validated);

        if (request()->wantsJson()) {
            return response()->json([
                'data' => new HostpinnacleAccountResource($account->fresh()),
                'message' => __('Hostpinnacle account updated.'),
            ]);
        }

        return redirect()->back()->with('success', __('Hostpinnacle account updated.'));
    }

    /**
     * Delete a Hostpinnacle account (owner-only).
     *
     * @param  HostpinnacleAccount  $account
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(HostpinnacleAccount $account): JsonResponse|RedirectResponse
    {
        $this->authorizeAccount($account);
        $account->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'message' => __('Hostpinnacle account deleted.'),
            ]);
        }

        return redirect()->back()->with('success', __('Hostpinnacle account deleted.'));
    }

    /**
     * Ensure the current user owns the account; abort 403 otherwise.
     * Comparison uses string cast so both integer and string (e.g. UUID) owner keys work.
     *
     * @param  HostpinnacleAccount  $account
     * @return void
     */
    protected function authorizeAccount(HostpinnacleAccount $account): void
    {
        $key = config('hostpinnacle.saas.owner_key', 'user_id');
        $ownerId = $this->ownerId();
        if ($ownerId === null || (string) $account->getAttribute($key) !== (string) $ownerId) {
            abort(403, __('You do not own this Hostpinnacle account.'));
        }
    }
}
