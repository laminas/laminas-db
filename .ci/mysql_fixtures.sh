#!/usr/bin/env bash

echo "Configure MySQL test database"

mysql -u root -pPassword123 -e 'create database zenddb_test;'
