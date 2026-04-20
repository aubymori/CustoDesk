CREATE TABLE rate_limits (
    ip TEXT,
    last_time INTEGER,
    expire_at INTEGER,
    allowance INTEGER
);

CREATE TABLE users (
    id INTEGER PRIMARY KEY NOT NULL,
    username CHAR(20),
    password TEXT,
    created_at INTEGER
);

CREATE TABLE sessions(
    id INTEGER PRIMARY KEY NOT NULL,
    secret TEXT,
    user_id INTEGER,
    created_at INTEGER,
    user_agent TEXT
);