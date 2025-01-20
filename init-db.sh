#!/bin/bash
set -e

mysql "${MYSQL_USER}" -p"${MYSQL_PASSWORD}" -e "CREATE DATABASE IF NOT EXISTS rating;"

mysql "${MYSQL_USER}" -p"${MYSQL_PASSWORD}" -e "CREATE DATABASE IF NOT EXISTS rating_test"

