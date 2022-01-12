#!/usr/bin/env bash

echo "Configure PostgreSQL test database"

psql -U postgres -c 'create database laminasdb_test;'
psql -U postgres -c "alter role postgres password 'postgres'"
