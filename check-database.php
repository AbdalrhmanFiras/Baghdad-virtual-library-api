<?php
/**
 * Script للتحقق من اتصال قاعدة البيانات وإنشائها إذا لزم الأمر
 * استخدم: php check-database.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $host = env('DB_HOST', '168.231.110.172');
    $port = env('DB_PORT', '4443');
    $database = env('DB_DATABASE', 'virtual_libaray');
    $username = env('DB_USERNAME', 'mysql');
    $password = env('DB_PASSWORD', '');
    
    echo "🔍 التحقق من اتصال قاعدة البيانات...\n";
    echo "Host: $host\n";
    echo "Port: $port\n";
    echo "Database: $database\n";
    echo "Username: $username\n\n";
    
    // الاتصال بدون تحديد قاعدة البيانات أولاً
    $pdo = new PDO(
        "mysql:host=$host;port=$port",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✅ الاتصال بنجاح!\n\n";
    
    // التحقق من وجود قاعدة البيانات
    $stmt = $pdo->query("SHOW DATABASES LIKE '$database'");
    $exists = $stmt->rowCount() > 0;
    
    if (!$exists) {
        echo "⚠️  قاعدة البيانات '$database' غير موجودة.\n";
        echo "🔨 جاري إنشاء قاعدة البيانات...\n";
        
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "✅ تم إنشاء قاعدة البيانات '$database' بنجاح!\n\n";
    } else {
        echo "✅ قاعدة البيانات '$database' موجودة.\n\n";
    }
    
    // التحقق من الصلاحيات
    echo "🔍 التحقق من الصلاحيات...\n";
    $pdo->exec("USE `$database`");
    echo "✅ المستخدم لديه صلاحيات على قاعدة البيانات!\n\n";
    
    // اختبار الاتصال باستخدام Laravel
    echo "🔍 اختبار الاتصال باستخدام Laravel...\n";
    DB::connection()->getPdo();
    echo "✅ Laravel متصل بقاعدة البيانات بنجاح!\n\n";
    
    // عرض الجداول الموجودة
    $tables = DB::select("SHOW TABLES");
    if (count($tables) > 0) {
        echo "📊 الجداول الموجودة:\n";
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            echo "   - $tableName\n";
        }
    } else {
        echo "ℹ️  لا توجد جداول في قاعدة البيانات.\n";
        echo "💡 قم بتشغيل: php artisan migrate --force\n";
    }
    
    echo "\n✅ كل شيء يعمل بشكل صحيح!\n";
    
} catch (PDOException $e) {
    echo "❌ خطأ في الاتصال: " . $e->getMessage() . "\n";
    echo "\n💡 الحلول المحتملة:\n";
    echo "1. تأكد من أن معلومات الاتصال صحيحة\n";
    echo "2. تأكد من أن المستخدم لديه صلاحيات\n";
    echo "3. تأكد من أن قاعدة البيانات موجودة\n";
    exit(1);
} catch (\Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    exit(1);
}
