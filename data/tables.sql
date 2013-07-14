-- create user vir with password 'vir';
-- create database vir_db with owner vir;

drop table if exists levels cascade;
create table levels (
       id               serial PRIMARY KEY,
       value            integer not null default 1,
       experience       integer not null default 0,
       experience_rate  integer not null default 1
);

drop table if exists stats cascade;
create table stats (
       id                 serial PRIMARY KEY,
       level_id           integer not null,
       name               varchar not null,
       value              integer not null default 0,
       adjustment         integer not null default 0,

       constraint stats_level_id_fkey foreign key (level_id) references levels(id) on delete cascade
);

drop table if exists primary_stats cascade;
create table primary_stats (
    id                  serial PRIMARY KEY,
    strength            integer not null default 5,
    perception          integer not null default 5,
    endurance           integer not null default 5,
    charisma            integer not null default 5,
    intelligence        integer not null default 5,
    agility             integer not null default 5,
    luck                integer not null default 5
);
