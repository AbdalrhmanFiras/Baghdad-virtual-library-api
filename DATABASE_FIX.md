# حل مشكلة قاعدة البيانات

## المشكلة:
```
SQLSTATE[HY000] [1044] Access denied for user 'mysql'@'%' to database 'virtual_libaray'
```

## الأسباب المحتملة:

### 1. قاعدة البيانات غير موجودة
قاعدة البيانات `virtual_libaray` غير موجودة في خادم MySQL.

**الحل:**
اتصل بخادم MySQL وأنشئ قاعدة البيانات:
```sql
CREATE DATABASE IF NOT EXISTS virtual_libaray CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. المستخدم لا يملك صلاحيات
المستخدم `mysql` لا يملك صلاحيات على قاعدة البيانات `virtual_libaray`.

**الحل:**
أعط المستخدم صلاحيات على قاعدة البيانات:
```sql
GRANT ALL PRIVILEGES ON virtual_libaray.* TO 'mysql'@'%';
FLUSH PRIVILEGES;
```

### 3. اسم قاعدة البيانات خاطئ
قد يكون هناك خطأ إملائي في اسم قاعدة البيانات.

**التحقق:**
```sql
SHOW DATABASES;
```

## الخطوات في Coolify:

### 1. تأكد من متغيرات البيئة:
في Coolify، تأكد من أن هذه المتغيرات صحيحة:
```
DB_CONNECTION=mysql
DB_HOST=168.231.110.172
DB_PORT=4443
DB_DATABASE=virtual_libaray
DB_USERNAME=mysql
DB_PASSWORD=your_password
```

### 2. إذا كانت قاعدة البيانات اسمها مختلف:
إذا كانت قاعدة البيانات اسمها `default` وليس `virtual_libaray`، غيّر:
```
DB_DATABASE=default
```

### 3. إنشاء قاعدة البيانات:
إذا لم تكن قاعدة البيانات موجودة، أنشئها من Coolify Terminal:
```bash
mysql -h 168.231.110.172 -P 4443 -u mysql -p
```

ثم:
```sql
CREATE DATABASE IF NOT EXISTS virtual_libaray CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON virtual_libaray.* TO 'mysql'@'%';
FLUSH PRIVILEGES;
EXIT;
```

### 4. تشغيل Migrations:
بعد إنشاء قاعدة البيانات:
```bash
php artisan migrate --force
```

## التحقق من الاتصال:

في Coolify Terminal:
```bash
php artisan tinker
```

ثم:
```php
DB::connection()->getPdo();
```

إذا نجح، ستحصل على معلومات الاتصال.

## ملاحظة مهمة:

⚠️ **تأكد من أن:**
- اسم قاعدة البيانات في Coolify يطابق اسم قاعدة البيانات الفعلية
- المستخدم لديه صلاحيات على قاعدة البيانات
- قاعدة البيانات موجودة
