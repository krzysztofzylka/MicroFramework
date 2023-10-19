alter table `account` modify `admin` tinyint(1) default 0 null after `api_key`;
alter table `common_file` modify `hash` varchar(128) null after `account_id`;
create index `common_file_account_id_hash_index` on `common_file` (`account_id`, `hash`);