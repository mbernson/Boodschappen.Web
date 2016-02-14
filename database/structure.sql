create table categories (
	id serial primary key,
	title text unique,
	parent integer default null references categories(id)
);

create type barcode_type as enum (
	'org.gs1.EAN-8',
	'org.gs1.EAN-13',
	'org.gs1.UPC-E'
);
create table products (
	id bigserial primary key,
	barcode text unique,
	barcode_type barcode_type not null,
	title text,
	category integer references categories(id),
);
create index idx_product_barcode on products(barcode);
create index idx_product_barcode_type on products(barcode_type);

create table chains (
	id serial primary key,
	title text unique
);

create table stores (
	id serial primary key,
	title text,
	chain integer references chains(id),
	city text,
	location coordinate
);

create table prices (
	product_id integer not null references products(id),
	store_id integer not null references store(id),
	price decimal not null,
	
	primary key (product_id, store_id)
);