#!/usr/bin/env bash

echo "Configure MySQL test database"

mysql --user=root --password=Password123 -e 'create database laminasdb_test;'
