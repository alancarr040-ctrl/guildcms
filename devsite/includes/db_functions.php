<?php
declare(strict_types=1);

/**
 * Create a mysqli connection for legacy/root site code.
 *
 * The main site now uses phpBB's database layer for most work, but this
 * function is kept as a small compatibility bridge for the root index.
 */
function mysql_init_connect(
    string $db_host,
    string $db_name,
    string $db_user,
    string $db_pass
): mysqli {
    $conn = mysqli_connect(
        $db_host,
        $db_user,
        $db_pass,
        $db_name
    );

    if (!$conn instanceof mysqli) {
        error_log('Database connection failed: ' . mysqli_connect_error());

        throw new RuntimeException('Database connection failed.');
    }

    if (!mysqli_set_charset($conn, 'utf8mb4')) {
        error_log('Database charset setup failed: ' . mysqli_error($conn));

        throw new RuntimeException('Database charset setup failed.');
    }

    return $conn;
}
