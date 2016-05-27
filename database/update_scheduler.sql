begin;
alter sequence schedule_crawl_seq restart with 1;
update schedule set last_crawled_at = now() - '2 days'::interval + ('00:00:10'::interval * (nextval('schedule_crawl_seq'::regclass)));

commit;
