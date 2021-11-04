#!/bin/bash

_setup() {
  for _ in {1..60}; do
    if /opt/mssql-tools/bin/sqlcmd -S 127.0.0.1 -U sa -P "$SA_PASSWORD" -d master -i scripts/setup.sql; then
      break
    fi
    echo "Server is not ready..."
    sleep 1
  done
}

echo "Starting SQL-Server on 127.0.0.1"
/opt/mssql/bin/mssql-conf set network.ipaddress 127.0.0.1
/opt/mssql/bin/sqlservr &
_setup

echo "Setup finished. Stopping SQL-Server..."
pid=$(pgrep -o sqlservr)
kill "$pid"
tail --pid="$pid" -f /dev/null

echo "Starting SQL-Server on 0.0.0.0"
/opt/mssql/bin/mssql-conf set network.ipaddress 0.0.0.0
/opt/mssql/bin/sqlservr