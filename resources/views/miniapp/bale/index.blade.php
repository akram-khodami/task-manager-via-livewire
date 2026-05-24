<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دستیار شخصی من</title>
    <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet">

    {{-- SDK مینی‌اپ بله - حتماً قبل از هر اسکریپت دیگه --}}
    <script src="https://tapi.bale.ai/miniapp.js?3"></script>

    <style>
        :root {
            --bg-color: #ffffff;
            --text-color: #212529;
            --card-bg: #f8f9fa;
            --primary-btn: #0d6efd;
            --primary-btn-text: #ffffff;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s ease;
        }

        .miniapp-container {
            max-width: 480px;
            margin: 0 auto;
            padding: 16px;
        }

        .feature-card {
            background: var(--card-bg);
            border: none;
            border-radius: 16px;
            transition: transform 0.2s;
            cursor: pointer;
        }

        .feature-card:hover {
            transform: translateY(-2px);
        }

        .feature-icon {
            font-size: 2rem;
            margin-bottom: 8px;
        }

        .btn-primary {
            background-color: var(--primary-btn);
            border-color: var(--primary-btn);
            color: var(--primary-btn-text);
        }

        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--bg-color);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.3s;
        }

        .loading-screen.hidden {
            opacity: 0;
            pointer-events: none;
        }
    </style>
</head>
<body>
{{-- اسکرین بارگذاری --}}
<div id="loadingScreen" class="loading-screen">
    <div class="text-center">
        <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">در حال بارگذاری...</span>
        </div>
        <h5>در حال بارگذاری...</h5>
    </div>
</div>

{{-- محتوای اصلی --}}
<div id="mainContent" class="miniapp-container" style="display: none;">
    {{-- هدر --}}
    <div class="text-center mb-4">
        <h4 class="fw-bold">دستیار شخصی من</h4>
        <p class="text-muted small">خوش آمدید!</p>
    </div>

    {{-- اطلاعات کاربر --}}
    <div class="card feature-card mb-4">
        <div class="card-body text-center">
            <div class="feature-icon">👤</div>
            <h5>پروفایل شما</h5>
            <p id="userInfo" class="text-muted small">در حال دریافت اطلاعات...</p>
        </div>
    </div>

    {{-- منوی ابزارها --}}
    <div class="row g-3">
        {{-- لیست کارها --}}
        <div class="col-6">
            <div class="card feature-card h-100" onclick="navigateTo('todos')">
                <div class="card-body text-center">
                    <div class="feature-icon">📝</div>
                    <h6>لیست کارها</h6>
                    <small class="text-muted">مدیریت وظایف</small>
                </div>
            </div>
        </div>

        {{-- پرداخت --}}
        <div class="col-6">
            <div class="card feature-card h-100" onclick="navigateTo('payment')">
                <div class="card-body text-center">
                    <div class="feature-icon">💳</div>
                    <h6>کیف پول</h6>
                    <small class="text-muted">پرداخت و اعتبار</small>
                </div>
            </div>
        </div>

        {{-- اسکنر QR --}}
        <div class="col-6">
            <div class="card feature-card h-100" onclick="navigateTo('scanner')">
                <div class="card-body text-center">
                    <div class="feature-icon">📷</div>
                    <h6>اسکنر QR</h6>
                    <small class="text-muted">اسکن کد</small>
                </div>
            </div>
        </div>

        {{-- تنظیمات --}}
        <div class="col-6">
            <div class="card feature-card h-100" onclick="navigateTo('settings')">
                <div class="card-body text-center">
                    <div class="feature-icon">⚙️</div>
                    <h6>تنظیمات</h6>
                    <small class="text-muted">تنظیمات ظاهری</small>
                </div>
            </div>
        </div>
    </div>

    {{-- دکمه‌های عملیاتی --}}
    <div class="mt-4">
        <button class="btn btn-outline-primary w-100 mb-2" onclick="requestContact()">
            <span class="icon-phone" style="font-size:1.5rem;">📞</span> دریافت شماره تماس
        </button>
        <button class="btn btn-outline-secondary w-100 mb-2" onclick="addToHomeScreen()">
            <span class="icon-plus" style="font-size:1.5rem;">➕</span> افزودن به صفحه اصلی
        </button>
    </div>
</div>

<script>
    // متغیرهای سراسری
    let BaleApp;
    let currentUser = null;

    // وقتی DOM آماده شد
    document.addEventListener('DOMContentLoaded', function () {
        initMiniApp();
    });

    function initMiniApp() {
        // بررسی وجود SDK
        if (!window.Bale || !window.Bale.WebApp) {
            showError('SDK بله بارگذاری نشده است');
            return;
        }

        BaleApp = window.Bale.WebApp;

        console.log('نسخه مینی‌اپ:', BaleApp.version);
        console.log('تم:', BaleApp.colorScheme);

        // اعمال تم
        applyTheme(BaleApp.themeParams);

        // دریافت و نمایش اطلاعات کاربر
        loadUserInfo();

        // آماده‌سازی مینی‌اپ
        BaleApp.ready();

        // مخفی کردن اسکرین بارگذاری
        setTimeout(() => {
            document.getElementById('loadingScreen').classList.add('hidden');
            document.getElementById('mainContent').style.display = 'block';
        }, 500);

        // تنظیم دکمه‌های هدر
        setupHeaderButtons();
    }

    function applyTheme(themeParams) {
        const root = document.documentElement;

        if (themeParams.bg_color) {
            root.style.setProperty('--bg-color', themeParams.bg_color);
        }
        if (themeParams.text_color) {
            root.style.setProperty('--text-color', themeParams.text_color);
        }
        if (themeParams.secondary_bg_color) {
            root.style.setProperty('--card-bg', themeParams.secondary_bg_color);
        }
        if (themeParams.button_color) {
            root.style.setProperty('--primary-btn', themeParams.button_color);
        }
        if (themeParams.button_text_color) {
            root.style.setProperty('--primary-btn-text', themeParams.button_text_color);
        }
    }

    //read
    function loadUserInfo() {
        if (BaleApp.initDataUnsafe && BaleApp.initDataUnsafe.user) {
            currentUser = BaleApp.initDataUnsafe.user;

            let userInfoHtml = `
                    ${currentUser.first_name || 'کاربر'}
                    ${currentUser.username ? '@' + currentUser.username : ''}
                `;

            document.getElementById('userInfo').innerHTML = userInfoHtml;

            // اعتبارسنجی داده‌ها در سرور
            validateWithServer(BaleApp.initData);
        }
    }

    //read
    async function validateWithServer(initData) {
        try {
            const response = await fetch('/api/miniapp/validate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({initData: initData})
            });

            const result = await response.json();
            console.log('نتیجه اعتبارسنجی:', result);
        } catch (error) {
            console.error('خطا در اعتبارسنجی:', error);
        }
    }

    function setupHeaderButtons() {
        // دکمه تنظیمات در منوی زمینه
        if (BaleApp.SettingsButton) {
            BaleApp.SettingsButton.show();
            BaleApp.SettingsButton.onClick(() => {
                navigateTo('settings');
            });
        }
    }

    //read3
    function navigateTo(page) {
        alert(`بخش "${page}" در گام‌های بعدی پیاده‌سازی می‌شود!`);
        // اینجا در آینده از Memory Router استفاده می‌کنیم
    }

    //read1
    function requestContact() {
        if (BaleApp.requestContact) {
            BaleApp.requestContact(function (shared, contact) {
                if (shared) {
                    alert('شماره تماس دریافت شد: ' + contact);//todo:dont use alert
                } else {
                    alert('کاربر اجازه دسترسی نداد');//todo:dont use alert
                }
            });
        }
    }

    //read2
    function addToHomeScreen() {
        if (BaleApp.addToHomeScreen) {
            BaleApp.addToHomeScreen();
        }
    }

    function showError(message) {
        document.getElementById('loadingScreen').innerHTML = `
                <div class="text-center text-danger">
                    <span class="icon-warning" style="font-size:3rem; color:#e69c00;">⚠️</span>
                    <h5 class="mt-3">خطا</h5>
                    <p>${message}</p>
                </div>
            `;
    }
</script>
</body>
</html>
