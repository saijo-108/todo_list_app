CREATE TABLE IF NOT EXISTS tasks (
	id INT auto_increment PRIMARY KEY,
    user_id int not null,
    title varchar(255) not null,
    description text,
    status varchar(30),
    due_date date,
    created_at timestamp default current_timestamp,
    updated_at timestamp default current_timestamp on update current_timestamp,
    foreign key fk_tasks_users (user_id) references users(id) on delete cascade on update cascade
);