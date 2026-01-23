<?php

namespace App\Http\Controllers;

use App\Models\ProfilePhoto;
use Illuminate\Support\Facades\Storage;

class ProfilePhotoController extends Controller
{
    public function destroy(ProfilePhoto $photo)
    {
        // 只允许本人删除
        $this->authorizePhoto($photo);

        // 删除文件
        if ($photo->path && Storage::disk('public')->exists($photo->path)) {
            Storage::disk('public')->delete($photo->path);
        }

        $photo->delete();

        return back()->with('success', 'Photo deleted.');
    }

    private function authorizePhoto(ProfilePhoto $photo): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        // 假设 Profile 通过 user_id 关联
        abort_unless(optional($photo->profile)->user_id === $user->id, 403);
    }
}
