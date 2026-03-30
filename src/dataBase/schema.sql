CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    token TEXT,
    password TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    image TEXT,
    content TEXT NOT NULL,
  author_id INTEGER NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id)
);


create table if not exists comments (
    id integer primary key autoincrement,
    post_id integer not null,
    author_id integer not null,
    content text not null,
    created_at text default current_timestamp,
    foreign key (post_id) references posts(id),
    foreign key (author_id) references users(id)
);