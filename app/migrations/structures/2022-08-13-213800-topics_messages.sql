create table topics_messages
(
    id serial,
    topics_id int not null
        constraint topics_messages_topics_id_fk
            references topics (id)
            on delete cascade,
    users_id int
        constraint topics_messages_users_id_fk
            references users
            on delete set null,
    comment text,
    date_added timestamptz not null
);

create unique index topics_messages_id_uindex
    on topics_messages (id);

alter table topics_messages
    add constraint topics_messages_pk
        primary key (id);