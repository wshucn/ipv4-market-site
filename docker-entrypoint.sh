#!/bin/bash
set -e

STORAGE_DIR="/var/www/html/storage"
echo "======== EFS Mount Debug Info ========"
echo "Check Directory: $STORAGE_DIR"

if [ -d "$STORAGE_DIR" ]; then
    echo "✅ Exists"
    
    if [ -w "$STORAGE_DIR" ]; then
        echo "✅ Writable"
    else
        echo "❌ Read-Only"
        ls -la $STORAGE_DIR
    fi
    
    TEST_FILE="$STORAGE_DIR/efs_test_$(date +%s).txt"
    if echo "EFS Test - $(date)" > "$TEST_FILE"; then
        echo "✅ Write Success: $TEST_FILE"
        cat "$TEST_FILE"
    else
        echo "❌ Write Failed: $TEST_FILE"
    fi
    
    echo "Contents Of Dir:"
    ls -la $STORAGE_DIR
else
    echo "❌ Directory Not Found"
    echo "Check Parent Directory:"
    ls -la /var/www/html
fi

echo "======== EFS Mount Test End ========"

exec apache2-foreground