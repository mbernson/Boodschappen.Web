CREATE OR REPLACE FUNCTION set_depth_column()
RETURNS TRIGGER AS $$
BEGIN
   NEW.depth = (select depth from generic_products where id = NEW.parent_id) + 1;
   RETURN NEW;
END;
$$ language 'plpgsql';


CREATE TRIGGER set_generic_products_depth_insert BEFORE INSERT
    ON generic_products FOR EACH ROW EXECUTE PROCEDURE
    set_depth_column();
CREATE TRIGGER set_generic_products_depth_update BEFORE UPDATE
    ON generic_products FOR EACH ROW EXECUTE PROCEDURE
    set_depth_column();
    
    update generic_products o set depth = (select depth from generic_products g where g.id = o.parent_id) where o.parent_id is not null;
    

CREATE OR REPLACE FUNCTION generic_products_full_subtree(root_id integer)
	returns table(
	id integer,
	title character varying(255),
	parent_id integer,
	depth integer,
	created_at timestamp
	)
AS $$
BEGIN
return query
with recursive child_categories as (
    select gp1.* from generic_products gp1
        where gp1.id = root_id
    union
    select gp2.*
        from generic_products gp2
        join child_categories
        on (gp2.parent_id = child_categories.id)
)
select cc.* from child_categories cc;
END;
$$ language 'plpgsql';


CREATE OR REPLACE FUNCTION generic_products_subtree(root_id integer)
	returns table(id integer, parent_id integer)
AS $$
BEGIN
return query
with recursive child_categories as (
    select gp1.id, gp1.parent_id from generic_products gp1
        where gp1.id = root_id
    union
    select gp2.id, gp2.parent_id
        from generic_products gp2
        join child_categories
        on (gp2.parent_id = child_categories.id)
)
select cc.id, cc.parent_id from child_categories cc;
END;
$$ language 'plpgsql';

-- select generic_products_subtree(151);