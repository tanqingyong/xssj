day=`date +%d`
monthago=`date -d"-10 day" +%Y-%m-%d`
monthdate=`date -d"-10 day" +%Y-%m`
today=`date -d"0 days" +%Y-%m-%d`


if [ "$day" = "11" ]
  then
mysql  -ubgzh -p'bgZH()90' --default-character-set=utf8 >xunshang.log  <<EOF
use zend;
set names utf8;
SELECT 
substr(date,1,7) AS 'yearmonth',
CASE WHEN gc.incity='全国' THEN gc.fromcity ELSE gc.incity END AS 'cityname',
sum(gc.offorderproductnum) AS 'salenum',sum(gc.offordersale) AS 'salemoney',sum(gc.profile) AS 'maoli' from goods_city_day gc
 WHERE day(DATE)>=01 AND day(DATE) < 11 and month='${monthdate}' GROUP BY yearmonth,cityname;
EOF

cat xunshang.log|awk  '{if(NR>1) print "insert into month_sale_data(month,city,early_goodsnum,early_money,early_profile) values(\042"$1"\042,\042"$2"\042,\042"$3"\042,\042"$4"\042,\042"$5"\042);"}'>xunshang.sql
mysql -ubgzh -p'bgZH()90' --default-character-set=utf8 zend <xunshang.sql

fi


if [ "$day" = "01" ]
  then
mysql  -ubgzh -p'bgZH()90' --default-character-set=utf8 >xunxia.log  <<EOF
use zend;
set names utf8;
SELECT 
substr(date,1,7) AS 'yearmonth',
CASE WHEN gc.incity='全国' THEN gc.fromcity ELSE gc.incity END AS 'cityname',
sum(gc.offorderproductnum) AS 'salenum',sum(gc.offordersale) AS 'salemoney',sum(gc.profile) AS 'maoli' from goods_city_day gc
 WHERE day(DATE)>=21 AND day(DATE) <= last_day(DATE) GROUP BY yearmonth,cityname;
EOF

cat xunxia.log|awk  '{if(NR>1) print "insert into month_data_end(month,city,end_goodsnum,end_money,end_profile) values(\042"$1"\042,\042"$2"\042,\042"$3"\042,\042"$4"\042,\042"$5"\042);"}'>xunxia.sql
mysql -ubgzh -p'bgZH()90' --default-character-set=utf8 zend <xunxia.sql

fi


if [ "$day" = "21" ]
  then
mysql  -ubgzh -p'bgZH()90' --default-character-set=utf8 >xunzhong.log  <<EOF
use zend;
set names utf8;
SELECT 
substr(date,1,7) AS 'yearmonth',
CASE WHEN gc.incity='全国' THEN gc.fromcity ELSE gc.incity END AS 'cityname',
sum(gc.offorderproductnum) AS 'salenum',sum(gc.offordersale) AS 'salemoney',sum(gc.profile) AS 'maoli' from goods_city_day gc
 WHERE day(DATE)>=11 AND day(DATE) < 21 GROUP BY yearmonth,cityname;
EOF


cat xunzhong.log|awk  '{if(NR>1) print "insert into month_data_mid(month,city,mid_goodsnum,mid_money,mid_profile) values(\042"$1"\042,\042"$2"\042,\042"$3"\042,\042"$4"\042,\042"$5"\042);"}'>xunzhong.sql
mysql -ubgzh -p'bgZH()90' --default-character-set=utf8 zend <xunzhong.sql


fi

#####update month data

mysql  -ubgzh -p'bgZH()90' --default-character-set=utf8   zend  <<EOF

INSERT INTO month_sale_data(month,city,mid_goodsnum,mid_money,mid_profile)
 SELECT month,city,mid_goodsnum,mid_money,mid_profile 
from month_data_mid WHERE city NOT IN(SELECT  city from month_sale_data WHERE month='${monthdate}') AND month='${monthdate}';

INSERT INTO month_sale_data(month,city,end_goodsnum,end_money,end_profile)
 SELECT month,city,end_goodsnum,end_money,end_profile 
from month_data_end WHERE city NOT IN(SELECT  city from month_sale_data WHERE month='${monthdate}') AND month='${monthdate}';

UPDATE month_sale_data a,month_data_mid b SET a.mid_goodsnum = b.mid_goodsnum,a.mid_money=b.mid_money,a.mid_profile=b.mid_profile
 WHERE a.city=b.city AND a.month=b.month and a.month='${monthdate}';

UPDATE month_sale_data a,month_data_end b SET a.end_goodsnum = b.end_goodsnum,a.end_money=b.end_money,a.end_profile=b.end_profile
 WHERE a.city=b.city AND a.month=b.month and a.month='${monthdate}';


update month_sale_data
  set month_goodsnum=(early_goodsnum+mid_goodsnum+end_goodsnum),month_money=(early_money+mid_money+end_money),month_profile=(early_profile+mid_profile+end_profile) where month='${monthdate}';

EOF
