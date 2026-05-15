<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BaleBotController extends Controller
{
    //Note: Introduce it`s api to Bale
    public function webhook(Request $request)
    {
        $update = $request->all();

        Log::info('Bale Update:', $update);

        //recognize it is a chat group or private chat
        $chatId = $update['message']['chat']['id'] ?? null;
        $text = $update['message']['text'] ?? '';
        $chatType = $update['message']['chat']['type'] ?? 'private'; // 'group' or 'private'

        if (!$chatId) {
            return response('OK', 200);
        }

        // 👇 فقط به پیام‌هایی که با @نام_ربات شروع شدن در گروه واکنش بده
        // (اینجوری ربات بیخودی تو گروه شلوغ‌کاری نمی‌کنه)
        $botUsername = 'BroDocsBot';
        $mention = "@{$botUsername}";

        //when bot is in group,it should just answer it is mentioned not every time
        if ($chatType === 'group') {
            if (!str_starts_with($text, $mention)) {
                return response('OK', 200);//it does not mentioned, so do noting
            }
            //remove mention part of text to get pure text
            $text = trim(str_replace($mention, '', $text));
        }

        // پردازش دستورات
        $response = $this->handleCommand($text, $chatId, $chatType);

        if ($response) {
            Bale::sendMessage($chatId, $response);
        }

        return response('OK', 200);
    }

    private function handleCommand(string $text, int $chatId, string $chatType): ?string
    {
        // 👋 Start
        if (in_array($text, ['/start', 'سلام', 'سلام ربات'])) {
            $name = $chatType === 'group' ? 'بچه‌های بروگرمرز' : 'دوست عزیز';
            return "سلام {$name}! 👋
            من ربات نمایش مستندات لاراول هستم.

📚 دستورات:
`/docs [نام بخش]` - نمایش مستندات
`/list` - لیست بخش‌های موجود
`/help` - راهنما

🔗 گروه بروگرمرز: @ProgMates";
        }

        // 📋 لیست بخش‌ها
        if ($text === '/list') {
            $docsPath = resource_path('docs');
            if (!is_dir($docsPath)) {
                return '❌ پوشه مستندات پیدا نشد.';
            }

            $files = glob($docsPath . '/*.md');
            $list = array_map(function ($file) {
                return '📄 ' . basename($file, '.md');
            }, $files);

            return empty($list)
                ? 'هنوز هیچ مستندی آپلود نشده.'
                : "📚 بخش‌های موجود:\n\n" . implode("\n", $list) .
                "\n\n📝 برای نمایش: `/docs نام_بخش`";
        }

        // 📖 نمایش مستندات
        if (str_starts_with($text, '/docs')) {
            $docName = trim(str_replace('/docs', '', $text));

            if (empty($docName)) {
                return "❌ لطفاً نام بخش رو مشخص کن.\nمثال: `/docs routing`";
            }

            $docPath = resource_path("docs/{$docName}.md");

            if (!file_exists($docPath)) {
                return "❌ بخش «{$docName}» پیدا نشد.\nاز `/list` برای دیدن بخش‌ها استفاده کن.";
            }

            $content = file_get_contents($docPath);

            // تلگرام/بله محدودیت ۴۰۹۶ کاراکتر دارن
            if (mb_strlen($content) > 4000) {
                $content = mb_substr($content, 0, 4000) . "\n\n... (ادامه دارد)";
            }

            return "📖 **{$docName}**\n\n" . $content;
        }

        // ❓ راهنما
        if ($text === '/help') {
            return "🤖 **راهنمای ربات بروگرمرز**

📌 دستورات:
`/docs routing` - مستندات مسیریابی
`/docs eloquent` - مستندات Eloquent
`/list` - همه بخش‌ها
`/help` - این راهنما

💡 در گروه با `@ProgDocsBot` منشن کن.";
        }

        // 🤷 پیام ناشناخته
        $name = $chatType === 'group' ? 'در گروه' : '';
        return "🤔 متوجه نشدم{$name}!\nاز `/help` برای راهنمایی استفاده کن.";
    }
}
