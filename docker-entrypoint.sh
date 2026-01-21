#!/bin/bash
set -e

# ضبط أذونات مجلدات Laravel
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# تشغيل أي أوامر أرسلت مع الحاوية أو Apache
if [ "$1" = 'apache2-foreground' ]; then
    exec apache2-foreground
else
    exec "$@"
fi
