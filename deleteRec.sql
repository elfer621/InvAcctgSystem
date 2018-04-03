delete from tbl_sales_items where counter=1 and receipt in (select receipt_id from tbl_sales_receipt_1 where `date` between '2017-01-01' and '2017-05-01');
delete from tbl_sales_receipt_1 where `date` between '2017-01-01' and '2017-05-01';


delete from tbl_stockin_items where stockin_refid in (select id from tbl_stockin_header where `date` between '2017-01-01' and '2017-03-30');
delete from tbl_stockin_header where `date` between '2017-01-01' and '2017-03-30';


delete from tbl_stockout_items where stockin_refid in (select id from tbl_stockout_header where `date` between '2017-01-01' and '2017-07-01');
delete from tbl_stockout_header where `date` between '2017-01-01' and '2017-07-01';

