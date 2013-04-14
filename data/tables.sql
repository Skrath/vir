-- create user vir with password 'vir';
-- create database vir_db with owner vir;

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
