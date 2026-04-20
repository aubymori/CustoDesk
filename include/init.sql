CREATE TABLE users (
    id INTEGER PRIMARY KEY,
    username CHAR(20),
    password TEXT,
    created_at INTEGER
);

CREATE TABLE sessions(
    id INTEGER PRIMARY KEY,
    secret TEXT,
    user_id INTEGER,
    created_at INTEGER,
    user_agent TEXT
);