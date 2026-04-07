<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProfilePhoto;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfilePhotoController extends Controller
{
    public function destroy($id)
    {
        try {
            // 查找模型，如果不存在会抛异常
            $photo = ProfilePhoto::findOrFail($id);

            // 删除存储文件，存在才删除
            if ($photo->path && Storage::exists($photo->path)) {
                Storage::delete($photo->path);
            }

            // 删除数据库记录
            $photo->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            // 写入日志，方便调试
            Log::error('Delete profile photo failed: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function authorizePhoto(ProfilePhoto $photo): void
    {
        $user = auth()->user();
        abort_unless($user, 403);

        // 假设 Profile 通过 user_id 关联
        abort_unless(optional($photo->profile)->user_id === $user->id, 403);
    }
}
