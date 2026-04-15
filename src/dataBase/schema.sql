CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    role TEXT NOT NULL DEFAULT 'user',
    password TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    image TEXT,
    content TEXT NOT NULL,
  author_id INTEGER NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    category_id INTEGER,
    FOREIGN KEY (category_id) REFERENCES categorys(id),
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


create table if not exists categorys (
    id integer primary key autoincrement,
    name text not null unique
);


insert into categorys (name) values ('Technology');
insert into categorys (name) values ('Health');
insert into categorys (name) values ('Travel');
insert into categorys (name) values ('Food');
insert into categorys (name) values ('Lifestyle');    
insert into categorys (name) values ('Education');
insert into categorys (name) values ('Entertainment');
insert into categorys (name) values ('Business');
insert into categorys (name) values ('Sports');
insert into categorys (name) values ('Games');