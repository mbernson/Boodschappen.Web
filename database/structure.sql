--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: barcode_type; Type: TYPE; Schema: public; Owner: boodschappen
--

CREATE TYPE barcode_type AS ENUM (
    'org.gs1.EAN-8',
    'org.gs1.EAN-13',
    'org.gs1.UPC-E'
);


ALTER TYPE public.barcode_type OWNER TO boodschappen;

--
-- Name: generic_products_full_subtree(integer); Type: FUNCTION; Schema: public; Owner: boodschappen
--

CREATE FUNCTION generic_products_full_subtree(root_id integer) RETURNS TABLE(id integer, title character varying, parent_id integer, depth integer, created_at timestamp without time zone)
    LANGUAGE plpgsql
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
$$;


ALTER FUNCTION public.generic_products_full_subtree(root_id integer) OWNER TO boodschappen;

--
-- Name: generic_products_subtree(integer); Type: FUNCTION; Schema: public; Owner: boodschappen
--

CREATE FUNCTION generic_products_subtree(root_id integer) RETURNS TABLE(id integer, parent_id integer)
    LANGUAGE plpgsql
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
$$;


ALTER FUNCTION public.generic_products_subtree(root_id integer) OWNER TO boodschappen;

--
-- Name: set_depth__column(); Type: FUNCTION; Schema: public; Owner: boodschappen
--

CREATE FUNCTION set_depth__column() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
   NEW.depth = (select depth from generic_products where parent_id = NEW.parent_id);
   RETURN NEW;
END;
$$;


ALTER FUNCTION public.set_depth__column() OWNER TO boodschappen;

--
-- Name: set_depth_column(); Type: FUNCTION; Schema: public; Owner: boodschappen
--

CREATE FUNCTION set_depth_column() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
   NEW.depth = (select depth from generic_products where id = NEW.parent_id) + 1;
   RETURN NEW;
END;
$$;


ALTER FUNCTION public.set_depth_column() OWNER TO boodschappen;

--
-- Name: update_updated_at_column(); Type: FUNCTION; Schema: public; Owner: boodschappen
--

CREATE FUNCTION update_updated_at_column() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
   NEW.created_at = now();
   RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_updated_at_column() OWNER TO boodschappen;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: companies; Type: TABLE; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE TABLE companies (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    logo_path character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.companies OWNER TO boodschappen;

--
-- Name: companies_id_seq; Type: SEQUENCE; Schema: public; Owner: boodschappen
--

CREATE SEQUENCE companies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.companies_id_seq OWNER TO boodschappen;

--
-- Name: companies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: boodschappen
--

ALTER SEQUENCE companies_id_seq OWNED BY companies.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE TABLE failed_jobs (
    id integer NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT ('now'::text)::timestamp(0) with time zone NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO boodschappen;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: boodschappen
--

CREATE SEQUENCE failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO boodschappen;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: boodschappen
--

ALTER SEQUENCE failed_jobs_id_seq OWNED BY failed_jobs.id;


--
-- Name: generic_products; Type: TABLE; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE TABLE generic_products (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    parent_id integer,
    depth integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.generic_products OWNER TO boodschappen;

--
-- Name: generic_products_id_seq; Type: SEQUENCE; Schema: public; Owner: boodschappen
--

CREATE SEQUENCE generic_products_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.generic_products_id_seq OWNER TO boodschappen;

--
-- Name: generic_products_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: boodschappen
--

ALTER SEQUENCE generic_products_id_seq OWNED BY generic_products.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE TABLE migrations (
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO boodschappen;

--
-- Name: password_resets; Type: TABLE; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE TABLE password_resets (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.password_resets OWNER TO boodschappen;

--
-- Name: price_changes; Type: TABLE; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE TABLE price_changes (
    id bigint,
    title character varying(255),
    prices numeric[],
    last_updated timestamp without time zone
);


ALTER TABLE public.price_changes OWNER TO boodschappen;

--
-- Name: prices; Type: TABLE; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE TABLE prices (
    product_id bigint NOT NULL,
    company_id integer,
    price numeric(8,2) NOT NULL,
    created_at timestamp(0) without time zone DEFAULT now() NOT NULL,
    CONSTRAINT check_minimum_price CHECK ((price > (0)::numeric))
);


ALTER TABLE public.prices OWNER TO boodschappen;

--
-- Name: products; Type: TABLE; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE TABLE products (
    id bigint NOT NULL,
    title character varying(255) NOT NULL,
    brand character varying(255) NOT NULL,
    unit_size character varying(255) NOT NULL,
    barcode character varying(255),
    generic_product_id integer NOT NULL,
    extended_attributes json,
    created_at timestamp(0) without time zone DEFAULT now() NOT NULL,
    updated_at timestamp(0) without time zone DEFAULT now() NOT NULL,
    barcode_type barcode_type,
    sku text NOT NULL,
    unit_amount numeric(8,3) DEFAULT 0 NOT NULL,
    bulk integer DEFAULT 1 NOT NULL,
    url character varying(255)
);


ALTER TABLE public.products OWNER TO boodschappen;

--
-- Name: products_id_seq; Type: SEQUENCE; Schema: public; Owner: boodschappen
--

CREATE SEQUENCE products_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.products_id_seq OWNER TO boodschappen;

--
-- Name: products_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: boodschappen
--

ALTER SEQUENCE products_id_seq OWNED BY products.id;


--
-- Name: scans; Type: TABLE; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE TABLE scans (
    id integer NOT NULL,
    user_id integer NOT NULL,
    barcode character varying(255) NOT NULL,
    created_at timestamp(0) without time zone DEFAULT now() NOT NULL,
    barcode_type barcode_type NOT NULL
);


ALTER TABLE public.scans OWNER TO boodschappen;

--
-- Name: scans_id_seq; Type: SEQUENCE; Schema: public; Owner: boodschappen
--

CREATE SEQUENCE scans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.scans_id_seq OWNER TO boodschappen;

--
-- Name: scans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: boodschappen
--

ALTER SEQUENCE scans_id_seq OWNED BY scans.id;


--
-- Name: schedule_crawl_seq; Type: SEQUENCE; Schema: public; Owner: boodschappen
--

CREATE SEQUENCE schedule_crawl_seq
    START WITH 1
    INCREMENT BY 2
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.schedule_crawl_seq OWNER TO boodschappen;

--
-- Name: schedule; Type: TABLE; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE TABLE schedule (
    id integer NOT NULL,
    query text NOT NULL,
    last_crawled_at timestamp without time zone DEFAULT ('2016-02-29 07:00:00'::timestamp without time zone + ('00:01:00'::interval * (nextval('schedule_crawl_seq'::regclass))::double precision)) NOT NULL
);


ALTER TABLE public.schedule OWNER TO boodschappen;

--
-- Name: schedule_id_seq; Type: SEQUENCE; Schema: public; Owner: boodschappen
--

CREATE SEQUENCE schedule_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.schedule_id_seq OWNER TO boodschappen;

--
-- Name: schedule_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: boodschappen
--

ALTER SEQUENCE schedule_id_seq OWNED BY schedule.id;


--
-- Name: shopping_list_has_product; Type: TABLE; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE TABLE shopping_list_has_product (
    list_id integer NOT NULL,
    product_id integer NOT NULL,
    created_at timestamp(0) without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.shopping_list_has_product OWNER TO boodschappen;

--
-- Name: shopping_lists; Type: TABLE; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE TABLE shopping_lists (
    id integer NOT NULL,
    user_id integer NOT NULL,
    title character varying(255) NOT NULL,
    count integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.shopping_lists OWNER TO boodschappen;

--
-- Name: shopping_lists_id_seq; Type: SEQUENCE; Schema: public; Owner: boodschappen
--

CREATE SEQUENCE shopping_lists_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.shopping_lists_id_seq OWNER TO boodschappen;

--
-- Name: shopping_lists_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: boodschappen
--

ALTER SEQUENCE shopping_lists_id_seq OWNED BY shopping_lists.id;


--
-- Name: stores; Type: TABLE; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE TABLE stores (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    chain_id integer NOT NULL,
    city character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.stores OWNER TO boodschappen;

--
-- Name: stores_id_seq; Type: SEQUENCE; Schema: public; Owner: boodschappen
--

CREATE SEQUENCE stores_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.stores_id_seq OWNER TO boodschappen;

--
-- Name: stores_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: boodschappen
--

ALTER SEQUENCE stores_id_seq OWNED BY stores.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE TABLE users (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(60) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO boodschappen;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: boodschappen
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO boodschappen;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: boodschappen
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: boodschappen
--

ALTER TABLE ONLY companies ALTER COLUMN id SET DEFAULT nextval('companies_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: boodschappen
--

ALTER TABLE ONLY failed_jobs ALTER COLUMN id SET DEFAULT nextval('failed_jobs_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: boodschappen
--

ALTER TABLE ONLY generic_products ALTER COLUMN id SET DEFAULT nextval('generic_products_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: boodschappen
--

ALTER TABLE ONLY products ALTER COLUMN id SET DEFAULT nextval('products_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: boodschappen
--

ALTER TABLE ONLY scans ALTER COLUMN id SET DEFAULT nextval('scans_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: boodschappen
--

ALTER TABLE ONLY schedule ALTER COLUMN id SET DEFAULT nextval('schedule_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: boodschappen
--

ALTER TABLE ONLY shopping_lists ALTER COLUMN id SET DEFAULT nextval('shopping_lists_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: boodschappen
--

ALTER TABLE ONLY stores ALTER COLUMN id SET DEFAULT nextval('stores_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: boodschappen
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Name: companies_pkey; Type: CONSTRAINT; Schema: public; Owner: boodschappen; Tablespace: 
--

ALTER TABLE ONLY companies
    ADD CONSTRAINT companies_pkey PRIMARY KEY (id);


--
-- Name: companies_title_unique; Type: CONSTRAINT; Schema: public; Owner: boodschappen; Tablespace: 
--

ALTER TABLE ONLY companies
    ADD CONSTRAINT companies_title_unique UNIQUE (title);


--
-- Name: failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: boodschappen; Tablespace: 
--

ALTER TABLE ONLY failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: generic_products_pkey; Type: CONSTRAINT; Schema: public; Owner: boodschappen; Tablespace: 
--

ALTER TABLE ONLY generic_products
    ADD CONSTRAINT generic_products_pkey PRIMARY KEY (id);


--
-- Name: generic_products_title_unique; Type: CONSTRAINT; Schema: public; Owner: boodschappen; Tablespace: 
--

ALTER TABLE ONLY generic_products
    ADD CONSTRAINT generic_products_title_unique UNIQUE (title);


--
-- Name: prices_pkey; Type: CONSTRAINT; Schema: public; Owner: boodschappen; Tablespace: 
--

ALTER TABLE ONLY prices
    ADD CONSTRAINT prices_pkey PRIMARY KEY (product_id, created_at);


--
-- Name: products_barcode_unique; Type: CONSTRAINT; Schema: public; Owner: boodschappen; Tablespace: 
--

ALTER TABLE ONLY products
    ADD CONSTRAINT products_barcode_unique UNIQUE (barcode);


--
-- Name: products_pkey; Type: CONSTRAINT; Schema: public; Owner: boodschappen; Tablespace: 
--

ALTER TABLE ONLY products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- Name: products_sku_key; Type: CONSTRAINT; Schema: public; Owner: boodschappen; Tablespace: 
--

ALTER TABLE ONLY products
    ADD CONSTRAINT products_sku_key UNIQUE (sku);


--
-- Name: scans_pkey; Type: CONSTRAINT; Schema: public; Owner: boodschappen; Tablespace: 
--

ALTER TABLE ONLY scans
    ADD CONSTRAINT scans_pkey PRIMARY KEY (id);


--
-- Name: schedule_query_key; Type: CONSTRAINT; Schema: public; Owner: boodschappen; Tablespace: 
--

ALTER TABLE ONLY schedule
    ADD CONSTRAINT schedule_query_key UNIQUE (query);


--
-- Name: shopping_list_has_product_pkey; Type: CONSTRAINT; Schema: public; Owner: boodschappen; Tablespace: 
--

ALTER TABLE ONLY shopping_list_has_product
    ADD CONSTRAINT shopping_list_has_product_pkey PRIMARY KEY (list_id, product_id);


--
-- Name: shopping_lists_pkey; Type: CONSTRAINT; Schema: public; Owner: boodschappen; Tablespace: 
--

ALTER TABLE ONLY shopping_lists
    ADD CONSTRAINT shopping_lists_pkey PRIMARY KEY (id);


--
-- Name: stores_pkey; Type: CONSTRAINT; Schema: public; Owner: boodschappen; Tablespace: 
--

ALTER TABLE ONLY stores
    ADD CONSTRAINT stores_pkey PRIMARY KEY (id);


--
-- Name: users_email_unique; Type: CONSTRAINT; Schema: public; Owner: boodschappen; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: boodschappen; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: idx_product_barcode_type; Type: INDEX; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE INDEX idx_product_barcode_type ON products USING btree (barcode_type);


--
-- Name: idx_sku; Type: INDEX; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE INDEX idx_sku ON products USING btree (sku);


--
-- Name: password_resets_email_index; Type: INDEX; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE INDEX password_resets_email_index ON password_resets USING btree (email);


--
-- Name: password_resets_token_index; Type: INDEX; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE INDEX password_resets_token_index ON password_resets USING btree (token);


--
-- Name: products_barcode_index; Type: INDEX; Schema: public; Owner: boodschappen; Tablespace: 
--

CREATE INDEX products_barcode_index ON products USING btree (barcode);


--
-- Name: _RETURN; Type: RULE; Schema: public; Owner: boodschappen
--

CREATE RULE "_RETURN" AS
    ON SELECT TO price_changes DO INSTEAD  SELECT products.id,
    products.title,
    array_agg(prices.price ORDER BY prices.created_at) AS prices,
    max(prices.created_at) AS last_updated
   FROM prices,
    products
  WHERE (((products.id = prices.product_id) AND (prices.product_id IN ( SELECT prices_1.product_id
           FROM prices prices_1
          GROUP BY prices_1.product_id
         HAVING (count(prices_1.product_id) > 1)))) AND (prices.price >= 0.05))
  GROUP BY products.id
  ORDER BY max(prices.created_at) DESC;


--
-- Name: set_generic_products_depth_insert; Type: TRIGGER; Schema: public; Owner: boodschappen
--

CREATE TRIGGER set_generic_products_depth_insert BEFORE INSERT ON generic_products FOR EACH ROW EXECUTE PROCEDURE set_depth_column();


--
-- Name: set_generic_products_depth_update; Type: TRIGGER; Schema: public; Owner: boodschappen
--

CREATE TRIGGER set_generic_products_depth_update BEFORE UPDATE ON generic_products FOR EACH ROW EXECUTE PROCEDURE set_depth_column();


--
-- Name: update_products_on_update; Type: TRIGGER; Schema: public; Owner: boodschappen
--

CREATE TRIGGER update_products_on_update BEFORE UPDATE ON products FOR EACH ROW EXECUTE PROCEDURE update_updated_at_column();


--
-- Name: fk_companies; Type: FK CONSTRAINT; Schema: public; Owner: boodschappen
--

ALTER TABLE ONLY prices
    ADD CONSTRAINT fk_companies FOREIGN KEY (company_id) REFERENCES companies(id);


--
-- Name: fk_generic_product; Type: FK CONSTRAINT; Schema: public; Owner: boodschappen
--

ALTER TABLE ONLY products
    ADD CONSTRAINT fk_generic_product FOREIGN KEY (generic_product_id) REFERENCES generic_products(id) ON DELETE RESTRICT;


--
-- Name: fk_generic_products_self; Type: FK CONSTRAINT; Schema: public; Owner: boodschappen
--

ALTER TABLE ONLY generic_products
    ADD CONSTRAINT fk_generic_products_self FOREIGN KEY (parent_id) REFERENCES generic_products(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: fk_products; Type: FK CONSTRAINT; Schema: public; Owner: boodschappen
--

ALTER TABLE ONLY prices
    ADD CONSTRAINT fk_products FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

