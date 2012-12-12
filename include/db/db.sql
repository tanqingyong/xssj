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
   user_num             int(16) not null comment '购买商品的用户数',
   suc_order_num        int(16) not null,
   suc_goods_num        int(16) not null,
   suc_total_price      float not null,
   suc_user_num         int(16) not null comment '支付成功的用户数，suc是success的前缀',
   positive_profile     double(10,2) comment '正毛利，该城市所有商品的正毛利之和',
   lose_profile         double(10,2) comment '负毛利',
   profile              double(10,2) comment '毛利',
   primary key (id)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

alter table buss_city comment '城市流量销售数据表';

/*==============================================================*/
/* Table: city                                                  */
/*==============================================================*/
create table city
(
   id                   smallint not null auto_increment comment '城市ID',
   area_id              smallint not null comment '区域ID',
   name                 varchar(30) not null comment '城市名称',
   name_py              varchar(30) comment '城市名称拼音',
   primary key (id, area_id)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

alter table city comment '城市表';

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
   cost_price           double(10,2) default 0.00 comment '结算价',
   positive_profile     double(10,2) default 0.00 comment '正毛利，一个商品要么有正毛利，要么有负毛利，不能两个都有',
   lose_profile         double(10,2) default 0.00 comment '负毛利，一个商品要么有正毛利，要么有负毛利，不能两个都有',
   profile              double(10,2) default 0.00 comment '毛利 ，为正毛利减去负毛利',
   primary key (id)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

alter table goods_city_day comment '产品城市流量销售数据表';

/*==============================================================*/
/* Table: region                                                */
/*==============================================================*/
create table region
(
   id                   smallint not null auto_increment comment '区域ID',
   name                 varchar(30) not null comment '区域名称',
   name_py              varbinary(30) comment '区域名称拼音',
   primary key (id)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

alter table region comment '大区';

/*==============================================================*/
/* Table: six_indicators                                        */
/*==============================================================*/
create table six_indicators
(
   id                   int not null auto_increment,
   indicator_date       int comment '指标所属月份，一个月一个城市只能有一条记录，格式为201108,使用时在代码里进行拆分',
   city                 varchar(50) comment '该指标所属城市',
   region               varchar(50) comment '该指标所属大区',
   pre_sale_amount      double(10,2) comment '预测销售额',
   pre_positive_profile double(10,2) comment '预测正毛利',
   pre_lose_profile     double precision(10,2) comment '预测负毛利，这里存的仍然是一个正数',
   pre_profile          double(10,2) comment '预测毛利,为预测正毛利减去预测负毛利',
   pre_profile_rate     double(5,2) comment '预测毛利率',
   pre_advance_payment  double(10,2) comment '预测预付款',
   is_modified_by_regioner tinyint default 0 comment '是否被大区经理编辑过.默认为0，大于0表示修改过',
   primary key (id)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;
create unique index date_city on six_indicators
(
   indicator_date,
   city
);
alter table six_indicators comment '六大指标表';

/*==============================================================*/
/* Table: users                                                 */
/*==============================================================*/
create table users
(
   id                   int(11) not null auto_increment comment '用户id，自增主键',
   username             varchar(20) not null comment '用户名，同时也是登录名，非空',
   create_by_id         int(3) default 0 comment '这个用户是由那个管理员创建，第一个管理员的创建者为0',
   password             varchar(255) not null comment '密码，存储的是md5加密后的',
   create_time          int(10) default 0 comment '用户创建时间',
   update_time          int(10) comment '用户信息更新时间，默认为创建时间',
   login_time           int(10) default 0 comment '用户上次登录时间',
   grade                tinyint default 1 comment '用户等级：1为普通用户(城市经理)，2为高级用户（大区经理），3为管理员, 4为总部权限',
   area_id              tinyint default 0 comment '该用户属于哪个大区，这里存放的大区ID，对应于大区表',
   city_id              smallint default 0 comment '该用户属于哪个城市，这里存放的是城市ID，对应于城市表',
   primary key (id)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8;

alter table users comment '销售数据挖掘项目用户表';

/*==============================================================*/
/* table: oa_contract                                           */
/*==============================================================*/
create table oa_contract
(
   id                   int(11) not null,
   contract_no          varchar(50) default NULL comment '合同编号',
   contract_type        int(11) default NULL comment '合同类型，0实物 1服务',
   city                 int(2) default NULL comment '城市ID',
   product_id           varchar(50) default NULL comment '产品ID',
   product_name         text default NULL comment '产品名称',
   start_date           date default NULL comment '上线时间',
   end_date             date default NULL comment '下线时间',
   effective_start_date date default NULL comment '团购有效期开始时间',
   effective_end_date   date default NULL comment '团购有效期结束时间',
   sell_price           decimal(12,2) default NULL comment '团购单价',
   settlement_price     decimal(12,2) default NULL comment '结算单价',
   partner_name         varchar(100) default NULL comment '客户名称',
   update_time          timestamp default current_timestamp comment '新建记录的时间',
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

alter table oa_contract comment '合同表';

/*==============================================================*/
/* table: udt_enumvalue OA枚举表         */
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
/* 销售数据平台：菜单访问日志及 在线用户监控                                           */
/*==============================================================*/
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
