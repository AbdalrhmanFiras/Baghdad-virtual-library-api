<?php
/**
 * Script للتحقق من قواعد البيانات المتاحة للمستخدم
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $host = env('DB_HOST', '168.231.110.172');
    $port = env('DB_PORT', '4443');
    $username = env('DB_USERNAME', 'mysql');
    $password = env('DB_PASSWORD', '');
    
    echo "🔍 التحقق من قواعد البيانات المتاحة...\n";
    echo "Host: $host\n";
    echo "Port: $port\n";
    echo "Username: $username\n\n";
    
    // الاتصال بدون تحديد قاعدة البيانات
    $pdo = new PDO(
        "mysql:host=$host;port=$port",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "✅ الاتصال بنجاح!\n\n";
    
    // عرض جميع قواعد البيانات المتاحة
    echo "📊 قواعد البيانات المتاحة:\n";
    $databases = $pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($databases as $db) {
        echo "   - $db\n";
    }
    
    echo "\n";
    
    // التحقق من الصلاحيات
    echo "🔍 الصلاحيات المتاحة:\n";
    $grants = $pdo->query("SHOW GRANTS FOR CURRENT_USER()")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($grants as $grant) {
        echo "   $grant\n";
    }
    
    echo "\n";
    
    // محاولة استخدام كل قاعدة بيانات
    echo "🔍 اختبار الوصول إلى قواعد البيانات:\n";
    foreach ($databases as $db) {
        try {
            $pdo->exec("USE `$db`");
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            echo "   ✅ $db - يمكن الوصول (عدد الجداول: " . count($tables) . ")\n";
        } catch (PDOException $e) {
            echo "   ❌ $db - لا يمكن الوصول: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n💡 استخدم قاعدة البيانات المتاحة أو اطلب من مدير قاعدة البيانات إنشاء 'virtual_libaray'\n";
    
} catch (PDOException $e) {
    echo "❌ خطأ في الاتصال: " . $e->getMessage() . "\n";
    exit(1);
}
