# �޸�users������ʶ���û�����״̬�ֶ�
alter table users add column online tinyint(1) default 0 comment '0-����  1-����';

# �˵���������־��¼
create table menu_log(
	id int(11) not null auto_increment comment '��־��¼ID',
	user_id int(11) not null comment '�û�id',
	ip	varchar(50) comment '�û�ip',
	menu_id int(11) not null comment '�˵�id',
	action_time int(11) comment '����ʱ��',
	primary key(id)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='�˵���������־��¼';

# �˵���
create table menu(
	id int(11) not null auto_increment,
	menu_name varchar(30) default null,
	parent_id int(11) default '0' ,
	menu_grade tinyint(1) DEFAULT NULL COMMENT '����1��ʾһ���˵���2��ʾ�����˵��������˵�������һ���˵�',
	url varchar(100) DEFAULT NULL,
	PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='�˵���';

# ά���˵����¼
INSERT INTO menu(id,menu_name,parent_id,menu_grade,url) VALUES 
(1,'ALL',0,0,NULL),
(2,'��������',1,1,NULL),
(3,'����ָ�걨��',1,1,NULL),
(4,'���۶�ë����ϸ',1,1,NULL),
(5,'�����ܼ�',2,2,'/manage/dataanalysis/summary.php'),
(6,'��������鿴',2,2,'/manage/dataanalysis/detail.php'),
(7,'����ָ��Ԥ�ⱨ��',3,2,'/manage/sixindex/index_forecast.php'),
(8,'��Ʒ������ϸ����',3,2,'/manage/sixindex/sales_detail.php'),
(9,'����ָ����ɱ���',3,2,'/manage/sixindex/index_completed.php'),
(10,'ȫ�����ۼ�',4,2,'/manage/moneydetail/nationwide_day.php'),
(11,'�������ۼ�',4,2,'/manage/moneydetail/region_day.php'),
(12,'�������ۼ�',4,2,'/manage/moneydetail/city_day.php'),
(13,'ȫ��Ѯ��',4,2,'/manage/moneydetail/month_sale_data_country.php'),
(14,'����Ѯ��',4,2,'/manage/moneydetail/month_sale_data_region.php'),
(15,'����Ѯ��',4,2,'/manage/moneydetail/month_sale_data_city.php');




