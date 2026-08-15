<?php
// Centralized Database Connection & Prepared Statement Helpers

if (!defined('DB_SERVER')) {
    define('DB_SERVER', getenv('DB_HOST') ?: (getenv('DB_SERVER') ?: 'localhost'));
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : (getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : ''));
    define('DB_NAME', getenv('DB_NAME') ?: (getenv('DB_DATABASE') ?: 'shopping'));
    define('DB_PORT', getenv('DB_PORT') ? intval(getenv('DB_PORT')) : 3306);
}

/**
 * Get or establish database connection
 * @return mysqli
 */
function get_db_connection() {
    static $con = null;
    if ($con === null || !($con instanceof mysqli) || @$con->ping() === false) {
        $port = defined('DB_PORT') ? DB_PORT : 3306;
        $con = @mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, $port);
        if (!$con) {
            error_log("Database connection failed: " . mysqli_connect_error());
            // Fail gracefully without leaking credentials/stack traces to clients
            die("Service temporarily unavailable. Please try again later.");
        }
        mysqli_set_charset($con, "utf8mb4");
    }
    return $con;
}

$con = get_db_connection();

/**
 * Execute a parameterized query with prepared statement
 * @param string $sql
 * @param array $params
 * @param string $types
 * @return mysqli_result|bool
 */
function db_query($sql, $params = [], $types = "") {
    $con = get_db_connection();
    if (empty($params)) {
        return mysqli_query($con, $sql);
    }
    
    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        error_log("Database prepare error: " . mysqli_error($con) . " in query: " . $sql);
        return false;
    }
    
    if (empty($types)) {
        $types = "";
        foreach ($params as $p) {
            if (is_int($p)) {
                $types .= "i";
            } elseif (is_float($p)) {
                $types .= "d";
            } else {
                $types .= "s";
            }
        }
    }
    
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    $executed = mysqli_stmt_execute($stmt);
    if (!$executed) {
        error_log("Database execute error: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return false;
    }
    
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false && mysqli_stmt_errno($stmt) === 0) {
        // Query had no result set (INSERT/UPDATE/DELETE)
        $affected = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        return true;
    }
    
    mysqli_stmt_close($stmt);
    return $result;
}

/**
 * Fetch all matching rows as associative array
 * @param string $sql
 * @param array $params
 * @param string $types
 * @return array
 */
function db_fetch_all($sql, $params = [], $types = "") {
    $res = db_query($sql, $params, $types);
    if (!$res || !($res instanceof mysqli_result)) {
        return [];
    }
    $rows = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $rows[] = $row;
    }
    mysqli_free_result($res);
    return $rows;
}

/**
 * Fetch a single matching row as associative array
 * @param string $sql
 * @param array $params
 * @param string $types
 * @return array|null
 */
function db_fetch_one($sql, $params = [], $types = "") {
    $res = db_query($sql, $params, $types);
    if (!$res || !($res instanceof mysqli_result)) {
        return null;
    }
    $row = mysqli_fetch_assoc($res);
    mysqli_free_result($res);
    return $row ? $row : null;
}

/**
 * Execute an INSERT/UPDATE/DELETE query (alias of db_query)
 * @param string $sql
 * @param array $params
 * @param string $types
 * @return bool|mysqli_result
 */
function db_execute($sql, $params = [], $types = "") {
    return db_query($sql, $params, $types);
}

/**
 * Get last inserted ID
 * @return int|string
 */
function db_insert_id() {
    $con = get_db_connection();
    return mysqli_insert_id($con);
}
?>
