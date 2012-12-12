# 修改users表，增标识加用户在线状态字段
alter table users add column online tinyint(1) default 0 comment '0-离线  1-在线';

# 菜单被操作日志记录
create table menu_log(
	id int(11) not null auto_increment comment '日志记录ID',
	user_id int(11) not null comment '用户id',
	ip	varchar(50) comment '用户ip',
	menu_id int(11) not null comment '菜单id',
	action_time int(11) comment '访问时间',
	primary key(id)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='菜单被操作日志记录';

# 菜单表
create table menu(
	id int(11) not null auto_increment,
	menu_name varchar(30) default null,
	parent_id int(11) default '0' ,
	menu_grade tinyint(1) DEFAULT NULL COMMENT '数字1表示一级菜单，2表示二级菜单，二级菜单隶属于一级菜单',
	url varchar(100) DEFAULT NULL,
	PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='菜单表';

# 维护菜单表记录
INSERT INTO menu(id,menu_name,parent_id,menu_grade,url) VALUES 
(1,'ALL',0,0,NULL),
(2,'流量数据',1,1,NULL),
(3,'六大指标报表',1,1,NULL),
(4,'销售额毛利明细',1,1,NULL),
(5,'流量总计',2,2,'/manage/dataanalysis/summary.php'),
(6,'流量详情查看',2,2,'/manage/dataanalysis/detail.php'),
(7,'六大指标预测报表',3,2,'/manage/sixindex/index_forecast.php'),
(8,'产品销售明细报表',3,2,'/manage/sixindex/sales_detail.php'),
(9,'六大指标完成报表',3,2,'/manage/sixindex/index_completed.php'),
(10,'全国日累计',4,2,'/manage/moneydetail/nationwide_day.php'),
(11,'大区日累计',4,2,'/manage/moneydetail/region_day.php'),
(12,'城市日累计',4,2,'/manage/moneydetail/city_day.php'),
(13,'全国旬月',4,2,'/manage/moneydetail/month_sale_data_country.php'),
(14,'大区旬月',4,2,'/manage/moneydetail/month_sale_data_region.php'),
(15,'城市旬月',4,2,'/manage/moneydetail/month_sale_data_city.php');




