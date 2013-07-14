create or replace function CreateStatLevel()
returns "trigger" as
$$
declare level_id LEVELS.id%TYPE;
begin
        insert into levels (experience_rate) values (1) returning id into level_id;

        new.level_id = level_id;

        return new;
end
$$
language 'plpgsql' volatile;

create trigger StatLevelTrigger
before insert on stats
for each row
execute procedure CreateStatLevel();
