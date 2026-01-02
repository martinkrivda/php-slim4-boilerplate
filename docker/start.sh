#!/bin/sh
set -eu

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
