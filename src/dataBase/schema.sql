CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    role TEXT NOT NULL DEFAULT 'user',
    password TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    image TEXT,
    content TEXT NOT NULL,
    author_id INTEGER NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    category_id INTEGER,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (author_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    post_id INTEGER NOT NULL,
    author_id INTEGER NOT NULL,
    content TEXT NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id),
    FOREIGN KEY (author_id) REFERENCES users(id)
);

INSERT INTO categories (name) VALUES ('Technology');
INSERT INTO categories (name) VALUES ('Health');
INSERT INTO categories (name) VALUES ('Travel');
INSERT INTO categories (name) VALUES ('Food');
INSERT INTO categories (name) VALUES ('Lifestyle');    
INSERT INTO categories (name) VALUES ('Education');
INSERT INTO categories (name) VALUES ('Entertainment');
INSERT INTO categories (name) VALUES ('Business');
INSERT INTO categories (name) VALUES ('Sports');
INSERT INTO categories (name) VALUES ('Games');