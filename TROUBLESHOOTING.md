# دليل حل مشاكل خطأ 500 Internal Server Error

## الأسباب الشائعة لخطأ 500:

### 1. متغيرات البيئة مفقودة أو غير صحيحة

**الحل:**
- تأكد من وجود جميع متغيرات البيئة في Coolify:
  ```
  APP_NAME=Laravel
  APP_ENV=production
  APP_KEY=base64:YOUR_KEY_HERE
  APP_DEBUG=false
  APP_URL=https://your-domain.com
  
  DB_CONNECTION=mysql
  DB_HOST=your_host
  DB_PORT=3306
  DB_DATABASE=your_database
  DB_USERNAME=your_username
  DB_PASSWORD=your_password
  
  JWT_SECRET=your_jwt_secret
  JWT_TTL=1440
  ```

### 2. قاعدة البيانات غير متصلة

**الحل:**
- تأكد من أن قاعدة البيانات متاحة من Coolify
- تحقق من إعدادات قاعدة البيانات في Coolify
- جرب الاتصال بقاعدة البيانات من داخل الحاوية:
  ```bash
  php artisan tinker
  DB::connection()->getPdo();
  ```

### 3. مشاكل في الصلاحيات

**الحل:**
- تأكد من أن مجلدات `storage` و `bootstrap/cache` لها الصلاحيات الصحيحة:
  ```bash
  chmod -R 775 storage bootstrap/cache
  chown -R www-data:www-data storage bootstrap/cache
  ```

### 4. مشاكل في الكاش

**الحل:**
- امسح جميع الكاشات:
  ```bash
  php artisan config:clear
  php artisan cache:clear
  php artisan route:clear
  php artisan view:clear
  ```

### 5. ملف .env غير موجود

**الحل:**
- تأكد من أن جميع متغيرات البيئة معرّفة في Coolify
- لا تحتاج ملف `.env` إذا كانت جميع المتغيرات في Coolify

### 6. مشاكل في Composer

**الحل:**
- تأكد من أن `composer install` تم بنجاح
- تحقق من ملف `composer.lock`

## كيفية عرض الأخطاء:

### في Coolify:
1. اذهب إلى Logs في Coolify
2. ابحث عن أخطاء PHP أو Laravel

### تفعيل وضع Debug مؤقتاً:
في Coolify، غيّر:
```
APP_DEBUG=true
```
⚠️ **تحذير:** لا تترك هذا في الإنتاج!

### عرض الأخطاء من داخل الحاوية:
```bash
# في Coolify Terminal
tail -f storage/logs/laravel.log
```

## خطوات التحقق السريعة:

1. ✅ تأكد من وجود جميع متغيرات البيئة
2. ✅ تحقق من اتصال قاعدة البيانات
3. ✅ تأكد من الصلاحيات
4. ✅ امسح الكاشات
5. ✅ تحقق من السجلات (logs)

## إذا استمرت المشكلة:

1. تحقق من `storage/logs/laravel.log`
2. تحقق من Apache error logs في Coolify
3. تأكد من أن PHP extensions مثبتة بشكل صحيح
4. تحقق من أن Composer dependencies مثبتة
