# الحل السريع - استخدام قاعدة البيانات default

## ✅ الحل:

قاعدة البيانات المتاحة هي `default`. تم تحديث الإعدادات لاستخدامها.

## الخطوات:

### 1. في Coolify - تأكد من متغيرات البيئة:

```
DB_CONNECTION=mysql
DB_HOST=168.231.110.172
DB_PORT=4443
DB_DATABASE=default
DB_USERNAME=mysql
DB_PASSWORD=rDDlkNAVzxO5bmpW0J3K9etsKtRvDYHhpaQ8OUa33W6O4bw0xtOL7V9MjjMU4BeJ
```

### 2. في Coolify Terminal - شغّل Migrations:

```bash
php artisan migrate --force
```

### 3. تحقق من الاتصال:

```bash
php artisan tinker
```

ثم:
```php
DB::connection()->getPdo();
// يجب أن يعمل بدون أخطاء
```

### 4. تحقق من الجداول:

```php
DB::select('SHOW TABLES');
```

## ✅ يجب أن يعمل الآن!

بعد تشغيل migrations، يجب أن يعمل التطبيق بدون مشاكل.
