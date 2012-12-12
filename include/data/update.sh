fishtime=`date -d"-1 day" +%Y-%m-%d`
mysql  -h10.8.210.62 -uxxread -p'xxread@123' >update.log  <<EOF
use jeehe55tuan;
set names utf8;
select jg.goods_id,jg.suppliers_id,js.suppliers_name,substr(jg.shop_price/jg.market_price,1,10),from_unixtime(jg.finish_time+28800) from jeehe_suppliers js INNER JOIN jeehe_goods jg ON js.suppliers_id=jg.suppliers_id WHERE from_unixtime(jg.finish_time+28800) >='${fishtime}';

EOF

cat update.log |awk '{if(NR>1) print "update goods_city_day set zhekou=\042"$4"\042,jiezhi_time=\042"$5"\042,biz_id=\042"$2"\042,biz_name=\042"$3"\042 where goodsid=\042"$1"\042;"}' >update.sql

mysql  -ubgzh -p'bgZH()90' --default-character-set=utf8   zend  <update.sql
