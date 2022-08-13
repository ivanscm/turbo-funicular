create table topics
(
    id    serial,
    title varchar(80),
    text  text
);

create unique index topics_id_uindex
    on topics (id);

create index topics_title_index
    on topics (title);

alter table topics
    add constraint topics_pk
        primary key (id);
