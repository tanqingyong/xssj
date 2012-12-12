/*==============================================================*/
/* DBMS name:      MySQL 5.0                                    */
/* Created on:     2011-09-22 19:12:15                          */
/*==============================================================*/


drop table if exists buss_city;

drop table if exists city;

drop table if exists dim_goods;

drop table if exists goods_city_day;

drop table if exists region;

drop table if exists six_indicators;

drop table if exists users;

drop table if exists oa_contract;

drop table if exists udt_enumvalue;

/*==============================================================*/
/* Table: buss_city                                             */
/*==============================================================*/
create table buss_city
(
   id                   int(16) not null auto_increment,
   data_date            date not null,
   city                 varchar(20) not null,
   ip                   varchar(20) not null,
   uv                   int(16) not null,
   pv                   int(16) not null,
   ad                   varchar(20) not null,
   order_num            int(16) not null,
   goods_num            int(16) not null,
   total_price          float not null,
   user_num             int(16) not null comment '������Ʒ���û���',
   suc_order_num        int(16) not null,
   suc_goods_num        int(16) not null,
   suc_total_price      float not null,
   suc_user_num         int(16) not null comment '֧���ɹ����û�����suc��success��ǰ׺',
   positive_profile     double(10,2) comment '��ë�����ó���������Ʒ����ë��֮��',
   lose_profile         double(10,2) comment '��ë��',
   profile              double(10,2) comment 'ë��',
   primary key (id)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

alter table buss_city comment '���������������ݱ�';

/*==============================================================*/
/* Table: city                                                  */
/*==============================================================*/
create table city
(
   id                   smallint not null auto_increment comment '����ID',
   area_id              smallint not null comment '����ID',
   name                 varchar(30) not null comment '��������',
   name_py              varchar(30) comment '��������ƴ��',
   primary key (id, area_id)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

alter table city comment '���б�';

/*==============================================================*/
/* Table: dim_goods                                             */
/*==============================================================*/
create table dim_goods
(
   id                   int(16) not null auto_increment,
   type1_id             varchar(20) not null,
   type1                varchar(20) not null,
   type2_id             varchar(20) not null,
   type2                varchar(40) not null,
   goods_id             varchar(20) not null,
   goods                varchar(256) not null,
   primary key (id)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

/*==============================================================*/
/* Table: goods_city_day                                        */
/*==============================================================*/
create table goods_city_day
(
   id                   int(11) not null auto_increment,
   DATE                 varchar(10) default NULL,
   goodsid              int(6) default NULL,
   goodsname            varchar(30) default NULL,
   incity               varchar(10) default NULL,
   fromcity             varchar(10) default NULL,
   firstcategoryid      int(3) default NULL,
   firstcategoryname    varchar(10) default NULL,
   begintime            varchar(10) default NULL,
   endtime              varchar(10) default NULL,
   ordernum             int(10) default NULL,
   orderproductnum      int(10) default NULL,
   ordersale            float default NULL,
   addupordersale       float default NULL,
   orderusernum         int(10) default NULL,
   offordernum          int(10) default NULL,
   offorderproductnum   int(10) default NULL,
   offordersale         float default NULL,
   offaddupordersale    float default NULL,
   offorderusernum      int(10) default NULL,
   PV                   int(10) default NULL,
   UV                   int(10) default NULL,
   IP                   int(10) default NULL,
   cost_price           double(10,2) default 0.00 comment '�����',
   positive_profile     double(10,2) default 0.00 comment '��ë����һ����ƷҪô����ë����Ҫô�и�ë����������������',
   lose_profile         double(10,2) default 0.00 comment '��ë����һ����ƷҪô����ë����Ҫô�и�ë����������������',
   profile              double(10,2) default 0.00 comment 'ë�� ��Ϊ��ë����ȥ��ë��',
   primary key (id)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

alter table goods_city_day comment '��Ʒ���������������ݱ�';

/*==============================================================*/
/* Table: region                                                */
/*==============================================================*/
create table region
(
   id                   smallint not null auto_increment comment '����ID',
   name                 varchar(30) not null comment '��������',
   name_py              varbinary(30) comment '��������ƴ��',
   primary key (id)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

alter table region comment '����';

/*==============================================================*/
/* Table: six_indicators                                        */
/*==============================================================*/
create table six_indicators
(
   id                   int not null auto_increment,
   indicator_date       int comment 'ָ�������·ݣ�һ����һ������ֻ����һ����¼����ʽΪ201108,ʹ��ʱ�ڴ�������в��',
   city                 varchar(50) comment '��ָ����������',
   region               varchar(50) comment '��ָ����������',
   pre_sale_amount      double(10,2) comment 'Ԥ�����۶�',
   pre_positive_profile double(10,2) comment 'Ԥ����ë��',
   pre_lose_profile     double precision(10,2) comment 'Ԥ�⸺ë������������Ȼ��һ������',
   pre_profile          double(10,2) comment 'Ԥ��ë��,ΪԤ����ë����ȥԤ�⸺ë��',
   pre_profile_rate     double(5,2) comment 'Ԥ��ë����',
   pre_advance_payment  double(10,2) comment 'Ԥ��Ԥ����',
   is_modified_by_regioner tinyint default 0 comment '�Ƿ񱻴�������༭��.Ĭ��Ϊ0������0��ʾ�޸Ĺ�',
   primary key (id)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;
create unique index date_city on six_indicators
(
   indicator_date,
   city
);
alter table six_indicators comment '����ָ���';

/*==============================================================*/
/* Table: users                                                 */
/*==============================================================*/
create table users
(
   id                   int(11) not null auto_increment comment '�û�id����������',
   username             varchar(20) not null comment '�û�����ͬʱҲ�ǵ�¼�����ǿ�',
   create_by_id         int(3) default 0 comment '����û������Ǹ�����Ա��������һ������Ա�Ĵ�����Ϊ0',
   password             varchar(255) not null comment '���룬�洢����md5���ܺ��',
   create_time          int(10) default 0 comment '�û�����ʱ��',
   update_time          int(10) comment '�û���Ϣ����ʱ�䣬Ĭ��Ϊ����ʱ��',
   login_time           int(10) default 0 comment '�û��ϴε�¼ʱ��',
   grade                tinyint default 1 comment '�û��ȼ���1Ϊ��ͨ�û�(���о���)��2Ϊ�߼��û�������������3Ϊ����Ա, 4Ϊ�ܲ�Ȩ��',
   area_id              tinyint default 0 comment '���û������ĸ������������ŵĴ���ID����Ӧ�ڴ�����',
   city_id              smallint default 0 comment '���û������ĸ����У������ŵ��ǳ���ID����Ӧ�ڳ��б�',
   primary key (id)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

alter table users comment '���������ھ���Ŀ�û���';

/*==============================================================*/
/* table: oa_contract                                           */
/*==============================================================*/
create table oa_contract
(
   id                   int(11) not null,
   contract_no          varchar(50) default NULL comment '��ͬ���',
   contract_type        int(11) default NULL comment '��ͬ���ͣ�0ʵ�� 1����',
   city                 int(2) default NULL comment '����ID',
   product_id           varchar(50) default NULL comment '��ƷID',
   product_name         text default NULL comment '��Ʒ����',
   start_date           date default NULL comment '����ʱ��',
   end_date             date default NULL comment '����ʱ��',
   effective_start_date date default NULL comment '�Ź���Ч�ڿ�ʼʱ��',
   effective_end_date   date default NULL comment '�Ź���Ч�ڽ���ʱ��',
   sell_price           decimal(12,2) default NULL comment '�Ź�����',
   settlement_price     decimal(12,2) default NULL comment '���㵥��',
   partner_name         varchar(100) default NULL comment '�ͻ�����',
   update_time          timestamp default current_timestamp comment '�½���¼��ʱ��',
   index `contract_no_ind` using btree (`contract_no`),
	 index `contract_type_ind` using btree (`contract_type`),
	 index `city_ind` using btree (`city`),
	 index `product_id_ind` using btree (`product_id`),
	 index `end_date_ind` using btree (`end_date`),
	 index `effective_end_date_ind` using btree (`effective_end_date`),
	 index `effective_start_date_ind` using btree (`effective_start_date`),
	 index `start_date_ind` using btree (`start_date`),
	 index `partner_name_ind` using btree (`partner_name`)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

alter table oa_contract comment '��ͬ��';

/*==============================================================*/
/* table: udt_enumvalue OAö�ٱ�         */
/*==============================================================*/
create table  `udt_enumvalue` (
  `enumid` int(11) not null default '0',
  `value` varchar(50) not null default '',
  `name` varchar(50) not null default '',
  `f_state` int(4) not null default '1',
  primary key (`enumid`,`value`)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;


alter table `goods_city_day` add `zhekou` varchar(10);
ALTER TABLE `goods_city_day` ADD `jiezhi_time` VARCHAR( 10 );
ALTER TABLE `goods_city_day` ADD `biz_id` VARCHAR( 20 );
ALTER TABLE `goods_city_day` ADD `biz_name` VARCHAR( 80 );


CREATE TABLE `month_sale_data` (
`month` VARCHAR( 7 ) NOT NULL ,
`city` VARCHAR( 30 ) NOT NULL ,
`month_goodsnum` VARCHAR( 30 ) NOT NULL ,
`month_money` VARCHAR( 30 ) NOT NULL ,
`month_profile` VARCHAR( 30 ) NOT NULL ,
`early_goodsnum` VARCHAR( 30 ) NOT NULL ,
`early_money` VARCHAR( 30 ) NOT NULL ,
`early_profile` VARCHAR( 30 ) NOT NULL ,
`mid_goodsnum` VARCHAR( 30 ) NOT NULL ,
`mid_money` VARCHAR( 30 ) NOT NULL ,
`mid_profile` VARCHAR( 30 ) NOT NULL ,
`end_goodsnum` VARCHAR( 30 ) NOT NULL ,
`end_money` VARCHAR( 30 ) NOT NULL ,
`end_profile` VARCHAR( 30 ) NOT NULL
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci ;

/*==============================================================*/
/* ��������ƽ̨���˵�������־�� �����û����                                           */
/*==============================================================*/
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
