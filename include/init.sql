CREATE TABLE IF NOT EXISTS rate_limits (
    ip TEXT,
    last_time INTEGER,
    expire_at INTEGER,
    allowance INTEGER
);

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY NOT NULL,
    username CHAR(20),
    password TEXT,
    created_at INTEGER,
    role INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS sessions(
    id INTEGER PRIMARY KEY NOT NULL,
    secret TEXT,
    user_id INTEGER,
    created_at INTEGER,
    user_agent TEXT
);

CREATE TABLE IF NOT EXISTS invite_keys(
    id INTEGER PRIMARY KEY NOT NULL,
    key CHAR(8),
    creator_id INTEGER,
    created_at INTEGER,
    user_id INTEGER,
    used_at INTEGER
);

CREATE TABLE IF NOT EXISTS alerts(
    id INTEGER PRIMARY KEY NOT NULL,
    text TEXT,
    type TEXT,
    creator_id INTEGER,
    created_at INTEGER
)