# حل مشكلة قاعدة البيانات

## المشكلة:
```
SQLSTATE[HY000] [1044] Access denied for user 'mysql'@'%' to database 'virtual_libaray'
```

أو:
```
SQLSTATE[42000]: Syntax error or access violation: 1044 Access denied for user 'mysql'@'%' to database 'virtual_libaray'
```

**السبب:** المستخدم `mysql` لا يملك صلاحيات لإنشاء قاعدة بيانات جديدة أو الوصول إلى `virtual_libaray`.

## الخطوة الأولى: التحقق من قواعد البيانات المتاحة

قبل محاولة إنشاء قاعدة بيانات جديدة، تحقق من القواعد المتاحة:

في Coolify Terminal:
```bash
php check-available-databases.php
```

هذا سيعرض:
- جميع قواعد البيانات المتاحة
- الصلاحيات المتاحة للمستخدم
- قواعد البيانات التي يمكنك الوصول إليها

## الأسباب المحتملة:

### 1. قاعدة البيانات غير موجودة والمستخدم لا يملك صلاحيات الإنشاء
المستخدم `mysql` لا يملك صلاحيات `CREATE DATABASE`.

**الحل:**
- اطلب من مدير قاعدة البيانات إنشاء قاعدة البيانات وإعطاء الصلاحيات
- أو استخدم قاعدة بيانات موجودة بالفعل

### 2. قاعدة البيانات موجودة لكن المستخدم لا يملك صلاحيات الوصول
المستخدم `mysql` لا يملك صلاحيات على قاعدة البيانات `virtual_libaray`.

**الحل:**
اطلب من مدير قاعدة البيانات إعطاء الصلاحيات:
```sql
GRANT ALL PRIVILEGES ON virtual_libaray.* TO 'mysql'@'%';
FLUSH PRIVILEGES;
```

### 3. استخدام قاعدة بيانات موجودة
إذا كانت هناك قاعدة بيانات أخرى متاحة، استخدمها:

في Coolify، غيّر متغير البيئة:
```
DB_DATABASE=اسم_قاعدة_البيانات_المتاحة
```

مثلاً إذا كانت `default` متاحة:
```
DB_DATABASE=default
```

### 4. اسم قاعدة البيانات خاطئ
قد يكون هناك خطأ إملائي في اسم قاعدة البيانات.

**التحقق:**
```bash
php check-available-databases.php
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

### 3. إنشاء قاعدة البيانات (بعد إعادة البناء):
بعد إعادة بناء Dockerfile (الذي يحتوي الآن على MySQL client)، استخدم:
```bash
mysql -h 168.231.110.172 -P 4443 -u mysql -p
```

أو استخدم السكريبت المدمج:
```bash
php check-database.php
```

السكريبت سيتحقق من الاتصال وينشئ قاعدة البيانات تلقائياً إذا لم تكن موجودة.

**بديل:** استخدم PHP مباشرة:
```bash
php artisan tinker
```
ثم:
```php
DB::statement('CREATE DATABASE IF NOT EXISTS virtual_libaray CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
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
