create table users
(
    id serial,
    title varchar(120)
);

create unique index users_id_uindex
    on users (id);

create index users_title_index
    on users (title);

alter table users
    add constraint users_pk
        primary key (id);