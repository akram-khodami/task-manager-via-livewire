<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MiniAppController extends Controller
{
    public function index()
    {
        return view('miniapp.bale.index');
    }

    public function validate(Request $request)
    {
        $initData = $request->input('initData');

        // اعتبارسنجی داده‌ها (در گام‌های بعدی کامل می‌شه)
        if (!$this->validateTelegramData($initData)) {
            return response()->json([
                'success' => false,
                'message' => 'داده‌های نامعتبر'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'اعتبارسنجی موفق'
        ]);
    }

    private function validateTelegramData($initData)
    {
        // این متد رو در گام اعتبارسنجی کامل می‌کنیم
        // فعلاً برای تست، true برمی‌گردونیم
        return true;
    }
}
