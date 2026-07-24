CREATE TABLE IF NOT EXISTS rate_limits (
    ip TEXT,
    last_time INTEGER,
    expire_at INTEGER,
    allowance INTEGER
);

CREATE TABLE IF NOT EXISTS users(
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
);

CREATE TABLE IF NOT EXISTS user_descriptions(
    user_id INTEGER PRIMARY KEY NOT NULL,
    html TEXT,
    source TEXT,
    editor TEXT,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS user_avatars(
    user_id INTEGER PRIMARY KEY NOT NULL,
    fname TEXT,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS user_follower_counts(
    user_id INTEGER PRIMARY KEY NOT NULL,
    followers_count INTEGER,
    following_count INTEGER,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS followers(
    id INTEGER PRIMARY KEY NOT NULL,
    from_id INTEGER,
    to_id INTEGER,
    created_at INTEGER,
    FOREIGN KEY(from_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(to_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS user_profiles(
    user_id INTEGER PRIMARY KEY NOT NULL,
    card_color CHAR(7),
    link_color CHAR(7),
    bg_color CHAR(7),
    bg_image TEXT,
    bg_fixed INTEGER,
    bg_repeat INTEGER,
    bg_align_x INTEGER,
    bg_align_y INTEGER
);