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


    {{-- این کد رو به view اصلی اضافه کن، داخل mainContent --}}

    {{-- بخش تنظیمات (در ابتدا hidden) --}}
    <div id="settingsPage" style="display: none;">
        <div class="mb-4">
            <button class="btn btn-outline-secondary btn-sm" onclick="goBack()">
                <i class="bi bi-arrow-right"></i> بازگشت
            </button>
            <h4 class="d-inline-block me-3">⚙️ تنظیمات</h4>
        </div>

        {{-- تنظیمات هدر --}}
        <div class="card feature-card mb-3">
            <div class="card-body">
                <h5 class="card-title">تنظیمات ظاهری</h5>

                <div class="mb-3">
                    <label class="form-label">رنگ هدر</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm flex-grow-1" style="background: #007AFF; color: white;"
                                onclick="changeHeaderColor('#007AFF')">آبی</button>
                        <button class="btn btn-sm flex-grow-1" style="background: #34C759; color: white;"
                                onclick="changeHeaderColor('#34C759')">سبز</button>
                        <button class="btn btn-sm flex-grow-1" style="background: #FF3B30; color: white;"
                                onclick="changeHeaderColor('#FF3B30')">قرمز</button>
                        <button class="btn btn-sm flex-grow-1" style="background: #FF9500; color: white;"
                                onclick="changeHeaderColor('#FF9500')">نارنجی</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">حالت تمام‌صفحه</label>
                    <button class="btn btn-outline-primary w-100" onclick="toggleFullscreen()">
                        <i class="bi bi-arrows-fullscreen"></i>
                        <span id="fullscreenBtnText">فعال‌سازی حالت تمام‌صفحه</span>
                    </button>
                </div>

                <div class="mb-3">
                    <label class="form-label">تأیید خروج</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="confirmClose"
                               onchange="toggleConfirmClose(this.checked)">
                        <label class="form-check-label" for="confirmClose">
                            هنگام بستن مینی‌اپ تأیید بگیر
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- تم فعلی --}}
        <div class="card feature-card mb-3">
            <div class="card-body">
                <h5 class="card-title">تم فعلی</h5>
                <div class="row" id="themeColors">
                    <!-- با جاوااسکریپت پر میشه -->
                </div>
            </div>
        </div>

        {{-- وضعیت میانبر --}}
        <div class="card feature-card mb-3">
            <div class="card-body">
                <h5 class="card-title">میانبر صفحه اصلی</h5>
                <div id="homeScreenStatus">
                    <button class="btn btn-outline-primary w-100" onclick="checkHomeScreen()">
                        <i class="bi bi-info-circle"></i> بررسی وضعیت میانبر
                    </button>
                    <div id="homeScreenResult" class="mt-2 small"></div>
                </div>
            </div>
        </div>

        {{-- اطلاعات نسخه --}}
        <div class="card feature-card">
            <div class="card-body">
                <h5 class="card-title">درباره</h5>
                <ul class="list-unstyled small mb-0">
                    <li>نسخه مینی‌اپ: <span id="miniappVersion">-</span></li>
                    <li>پلتفرم: <span id="platformType">-</span></li>
                    <li>تم سیستم: <span id="colorScheme">-</span></li>
                    <li>پشتیبانی مینی‌اپ: <span id="miniAppSupported">-</span></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- بخش اسکنر QR (در ابتدا hidden) --}}
    <div id="scannerPage" style="display: none;">
        <div class="mb-4">
            <button class="btn btn-outline-secondary btn-sm" onclick="goBack()">
                <i class="bi bi-arrow-right"></i> بازگشت
            </button>
            <h4 class="d-inline-block me-3">📷 اسکنر QR</h4>
        </div>

        {{-- دکمه اسکن --}}
        <div class="card feature-card mb-3">
            <div class="card-body text-center">
                <div class="feature-icon" style="font-size: 4rem;">📷</div>
                <h5>اسکن کد QR</h5>
                <p class="text-muted small">دوربین رو به سمت QR code بگیرید</p>
                <button class="btn btn-primary w-100" onclick="startScanning()">
                    <i class="bi bi-qr-code-scan"></i> شروع اسکن
                </button>
            </div>
        </div>

        {{-- نتیجه اسکن --}}
        <div id="scanResult" style="display: none;">
            <div class="card feature-card mb-3">
                <div class="card-body">
                    <h5 class="card-title">✅ نتیجه اسکن</h5>
                    <div class="p-3 bg-light rounded">
                        <code id="scanText" class="text-break"></code>
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-outline-primary btn-sm" onclick="copyScanResult()">
                            <i class="bi bi-clipboard"></i> کپی
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="openScannedLink()">
                            <i class="bi bi-box-arrow-up-right"></i> باز کردن لینک
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- تاریخچه اسکن‌ها --}}
        <div class="card feature-card">
            <div class="card-body">
                <h5 class="card-title">📋 تاریخچه اسکن‌ها</h5>
                <div id="scanHistory">
                    <p class="text-muted small">هنوز اسکنی انجام نشده</p>
                </div>
            </div>
        </div>
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

<script>
    // این کد رو به بخش script view اضافه کن

    // ======= مدیریت ناوبری (Memory Router ساده) =======
    let currentPage = 'home';
    let pageHistory = ['home'];

    function navigateTo(page) {
        // مخفی کردن همه صفحات
        document.getElementById('mainContent').style.display = 'none';
        document.getElementById('settingsPage').style.display = 'none';
        document.getElementById('scannerPage').style.display = 'none';

        // نمایش صفحه مورد نظر
        switch(page) {
            case 'settings':
                document.getElementById('settingsPage').style.display = 'block';
                loadSettings();
                break;
            case 'scanner':
                document.getElementById('scannerPage').style.display = 'block';
                break;
            default:
                document.getElementById('mainContent').style.display = 'block';
        }

        // ذخیره تاریخچه
        pageHistory.push(page);
        currentPage = page;

        // نمایش دکمه بازگشت در هدر
        if (page !== 'home' && BaleApp.BackButton) {
            BaleApp.BackButton.show();
        }
    }

    function goBack() {
        // حذف صفحه فعلی از تاریخچه
        pageHistory.pop();

        // برگشت به صفحه قبلی
        const previousPage = pageHistory[pageHistory.length - 1] || 'home';

        // مخفی کردن دکمه بازگشت اگر برگشتیم به خانه
        if (previousPage === 'home' && BaleApp.BackButton) {
            BaleApp.BackButton.hide();
        }

        navigateTo(previousPage);
    }

    // گوش دادن به دکمه بازگشت بله
    if (BaleApp && BaleApp.BackButton) {
        BaleApp.BackButton.onClick(() => {
            goBack();
        });
    }

    // ======= تنظیمات =======
    function changeHeaderColor(color) {
        if (BaleApp.setHeaderColor) {
            BaleApp.setHeaderColor(color);
            showToast('رنگ هدر با موفقیت تغییر کرد 🎨');
        }
    }

    function toggleFullscreen() {
        const btn = document.getElementById('fullscreenBtnText');
        if (btn.textContent.includes('فعال')) {
            if (BaleApp.expand) {
                BaleApp.expand();
                btn.textContent = 'غیرفعال‌سازی حالت تمام‌صفحه';
                showToast('حالت تمام‌صفحه فعال شد 📱');
            }
        } else {
            // متأسفانه API برای غیرفعال کردن expand وجود نداره
            showToast('برای خروج از حالت تمام‌صفحه، مینی‌اپ رو ببندید و دوباره باز کنید');
        }
    }

    function toggleConfirmClose(enabled) {
        if (enabled) {
            if (BaleApp.enableClosingConfirmation) {
                BaleApp.enableClosingConfirmation();
                showToast('تأیید خروج فعال شد 🔒');
            }
        } else {
            if (BaleApp.disableClosingConfirmation) {
                BaleApp.disableClosingConfirmation();
                showToast('تأیید خروج غیرفعال شد 🔓');
            }
        }
    }

    function checkHomeScreen() {
        if (BaleApp.checkHomeScreenStatus) {
            BaleApp.checkHomeScreenStatus(function(status) {
                const resultDiv = document.getElementById('homeScreenResult');
                let message = '';

                switch(status) {
                    case 'unsupported':
                        message = '❌ دستگاه شما از این قابلیت پشتیبانی نمی‌کند';
                        break;
                    case 'unknown':
                        message = '⚠️ وضعیت میانبر قابل تشخیص نیست';
                        break;
                    case 'added':
                        message = '✅ میانبر قبلاً به صفحه اصلی اضافه شده';
                        break;
                    case 'missed':
                        message = '➕ میانبر اضافه نشده. می‌توانید اضافه کنید';
                        break;
                }

                resultDiv.innerHTML = `<div class="alert alert-info">${message}</div>`;
            });
        }
    }

    function loadSettings() {
        // پر کردن اطلاعات سیستم
        if (BaleApp) {
            document.getElementById('miniappVersion').textContent = BaleApp.version || 'نامشخص';
            document.getElementById('platformType').textContent = BaleApp.isIframe ? 'وب' : 'اندروید';
            document.getElementById('colorScheme').textContent = BaleApp.colorScheme || 'نامشخص';
            document.getElementById('miniAppSupported').textContent = BaleApp.isMiniAppSupported ? '✅ بله' : '❌ خیر';

            // نمایش رنگ‌های تم
            if (BaleApp.themeParams) {
                const colors = BaleApp.themeParams;
                let html = '';

                const colorMap = {
                    'bg_color': 'پس‌زمینه اصلی',
                    'text_color': 'رنگ متن',
                    'hint_color': 'متن راهنما',
                    'link_color': 'لینک‌ها',
                    'button_color': 'دکمه‌ها',
                    'button_text_color': 'متن دکمه',
                    'secondary_bg_color': 'پس‌زمینه ثانویه'
                };

                for (const [key, value] of Object.entries(colorMap)) {
                    if (colors[key]) {
                        html += `
                        <div class="col-6 mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width: 24px; height: 24px; background: ${colors[key]};
                                            border-radius: 50%; border: 1px solid #ddd;"></div>
                                <small>${value}</small>
                            </div>
                        </div>
                    `;
                    }
                }

                document.getElementById('themeColors').innerHTML = html;
            }
        }
    }

    // ======= اسکنر QR =======
    let lastScannedText = '';

    function startScanning() {
        if (BaleApp.showScanQrPopup) {
            BaleApp.showScanQrPopup({
                text: 'کد QR را اسکن کنید'
            });
            showToast('اسکنر فعال شد. دوربین را به سمت QR بگیرید 📷');
        } else {
            showToast('❌ دستگاه شما از اسکنر پشتیبانی نمی‌کند');
        }
    }

    // گوش دادن به نتیجه اسکن
    if (BaleApp) {
        BaleApp.onEvent('qrTextReceived', function(data) {
            lastScannedText = data.text;

            // نمایش نتیجه
            document.getElementById('scanResult').style.display = 'block';
            document.getElementById('scanText').textContent = data.text;

            // اضافه به تاریخچه
            addToScanHistory(data.text);

            showToast('✅ اسکن با موفقیت انجام شد');
        });

        BaleApp.onEvent('scanQrPopupClosed', function() {
            showToast('اسکنر بسته شد');
        });
    }

    function addToScanHistory(text) {
        const historyDiv = document.getElementById('scanHistory');
        const time = new Date().toLocaleTimeString('fa-IR');

        const historyItem = `
        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
            <small class="text-truncate" style="max-width: 200px;">${text}</small>
            <small class="text-muted">${time}</small>
        </div>
    `;

        if (historyDiv.querySelector('.text-muted')) {
            historyDiv.innerHTML = '';
        }

        historyDiv.insertAdjacentHTML('afterbegin', historyItem);
    }

    function copyScanResult() {
        navigator.clipboard.writeText(lastScannedText).then(() => {
            showToast('📋 متن کپی شد');
        });
    }

    function openScannedLink() {
        if (lastScannedText.startsWith('http')) {
            if (BaleApp.openLink) {
                BaleApp.openLink(lastScannedText);
            } else {
                window.open(lastScannedText, '_blank');
            }
        } else {
            showToast('⚠️ متن اسکن شده یک لینک معتبر نیست');
        }
    }

    // ======= توابع کمکی =======
    function showToast(message) {
        // ایجاد toast با بوت‌استرپ
        const toastHTML = `
        <div class="position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index: 9999">
            <div class="toast align-items-center text-bg-primary border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    `;

        document.body.insertAdjacentHTML('beforeend', toastHTML);
        const toastElement = document.querySelector('.toast:last-child');
        const toast = new bootstrap.Toast(toastElement, { delay: 2000 });
        toast.show();

        // پاکسازی بعد از بسته شدن
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.parentElement.remove();
        });
    }
</script>
</body>
</html>
