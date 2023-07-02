alter table `account` modify `limit_usage` int unsigned default 0 null;
alter table `account_remind_password` modify `account_id` int unsigned null;
alter table `database_updater` modify `name` varchar(15) not null;