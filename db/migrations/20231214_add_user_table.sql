CREATE TABLE IF NOT EXISTS users (
	id INT auto_increment PRIMARY KEY,
    username varchar(255) not null,
    hashed_password varchar(255) not null,
    email varchar(255) unique,
    created_at timestamp default current_timestamp,
    updated_at timestamp default current_timestamp on update current_timestamp
    );