<?php

namespace App\Http\Controllers;

use App\Helper\FileHelper;
use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @tags Profile Endpoint
 */
class ProfileController extends Controller
{
    /**
     * Create Profile
     */
    public function store(StoreProfileRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();
        $data['user_id'] = $user->id;
        $data['email'] = $user->email;

        if (Profile::where('user_id', $user->id)->exists()) {
            return $this->responseError(null, 'Profile already created', 200);
        }

        $profile = Profile::create($data);
        $file = $request->hasFile('image') ? $request->file('image') : null;
        $path = FileHelper::ImageUpload($file, 'profiles', null, 's3');
        $profile->image()->create([
            'url' => $path,
            'type' => 'profile',
        ]);
        $profile = $profile->fresh('image');

        return response()->json([
            'message' => 'Profile created successfully',
            'data' => new ProfileResource($profile),
        ], 201);
    }

    /**
     * Update Profile
     */
    public function update(UpdateProfileRequest $request)
    {
        $data = $request->validated();
        try {
            $profile = Profile::where('user_id', Auth::id())
                ->firstOrFail();
            DB::transaction(function () use ($request, $profile, $data) {
                if ($request->hasFile('image')) {
                    $file = $request->file('image');

                    $path = FileHelper::ImageUpload($file, 'profiles', null, 's3');

                    FileHelper::UpdateImage($profile, $path, 's3');
                }
                $profile->update($data);
            });

            return response()->json([
                'message' => 'Profile updated successfully',
                'data' => new ProfileResource($profile->fresh('image')),
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Profile not found',
            ], 404);
        }
    }

    /**
     * Show(get) Profile
     */
    public function show()
    {
        $profile = Profile::where('user_id', Auth::id())->first();
        if (! $profile) {
            return response()->json(['message' => 'Profile not found'], 404);
        }

        return response()->json([
            'message' => 'Profile fetched successfully',
            'data' => new ProfileResource($profile),
        ], 200);
    }
}
