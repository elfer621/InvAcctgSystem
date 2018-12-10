/* tbl_reading_server
--column in tbl_reading_server
--tbl_sales_splitpayment */


ALTER TABLE `tbl_product_name`
	ADD COLUMN `tax_type` VARCHAR(50) NOT NULL AFTER `subjtype`;

ALTER TABLE `tbl_reading`
	ADD COLUMN `start_time` TIME NOT NULL AFTER `start_date`,
	ADD COLUMN `end_time` TIME NOT NULL AFTER `end_date`;

	
/*--1/9/17*/
tbl_sales_receipt_
added category_id int 5

ALTER TABLE `tbl_sales_receipt_1`
	ADD COLUMN `category_id` INT NOT NULL AFTER `yr`;
	
/*--1/25/17	*/
CREATE TABLE `tbl_inv_allbranch` (
	`sku_id` INT(11) NOT NULL,
	`uclm` DOUBLE NULL DEFAULT NULL,
	`ucmambaling` DOUBLE NULL DEFAULT NULL,
	`ucmain` DOUBLE NULL DEFAULT NULL,
	`ucbanilad` DOUBLE NULL DEFAULT NULL,
	`warehouse` DOUBLE NULL DEFAULT NULL,
	PRIMARY KEY (`sku_id`)
)
ENGINE=InnoDB
;

/*--2-7-17 SOA changes*/

ALTER TABLE `tbl_soa`
	ADD COLUMN `year` YEAR NULL DEFAULT NULL AFTER `cust_id`,
	ADD COLUMN `month` INT NULL DEFAULT NULL AFTER `year`;
ALTER TABLE `tbl_soa`
	ADD UNIQUE INDEX `cust_id_year_month` (`cust_id`, `year`, `month`);
	
CREATE TABLE `tbl_soa_prev_bal` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`soa_num` INT(11) NULL DEFAULT NULL,
	`cust_id` INT(11) NULL DEFAULT NULL,
	`details` VARCHAR(150) NULL DEFAULT NULL,
	`amount` DOUBLE NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;

ALTER TABLE `tbl_soa_current_charges`
	ADD COLUMN `class` VARCHAR(50) NULL DEFAULT NULL AFTER `amount`;
ALTER TABLE `tbl_soa_other_charges`
	ADD COLUMN `class` VARCHAR(50) NULL DEFAULT NULL AFTER `amount`;
ALTER TABLE `tbl_soa_prev_bal`
	ADD COLUMN `class` VARCHAR(50) NULL DEFAULT NULL AFTER `amount`;
ALTER TABLE `tbl_soa_reimbursable_charges`
	ADD COLUMN `class` VARCHAR(50) NULL DEFAULT NULL AFTER `amount_due`;

ALTER TABLE `tbl_soa`
	ADD COLUMN `payment_ref` INT NULL DEFAULT NULL AFTER `due_date`;
ALTER TABLE `tbl_soa`
	ADD COLUMN `vouchering_ref` INT(11) NULL DEFAULT NULL AFTER `payment_ref`;
/*--2-7-17 SOA changes*/	
CREATE TABLE `tbl_master_details` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(50) NULL DEFAULT NULL,
	`column_master` TEXT NULL,
	`tbl_master` TEXT NULL,
	`primary_col_master` VARCHAR(50) NULL DEFAULT NULL,
	`column_details` TEXT NULL,
	`tbl_details` TEXT NULL,
	`primary_col_details` VARCHAR(50) NULL DEFAULT NULL,
	`nolimit` VARCHAR(50) NULL DEFAULT NULL,
	`isgrouping` VARCHAR(50) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=5
;

INSERT INTO `tbl_master_details` (`id`, `title`, `column_master`, `tbl_master`, `primary_col_master`, `column_details`, `tbl_details`, `primary_col_details`, `nolimit`, `isgrouping`) VALUES (1, 'Receipt Lookup', 'receipt_id,date,studentname,amount,vat,type,counter_num', 'view_receipt', 'counter_num|receipt_id', 'receipt,counter,item_desc,qty,unit,selling,total', 'tbl_sales_items', 'counter|receipt', NULL, NULL);
INSERT INTO `tbl_master_details` (`id`, `title`, `column_master`, `tbl_master`, `primary_col_master`, `column_details`, `tbl_details`, `primary_col_details`, `nolimit`, `isgrouping`) VALUES (2, 'StockIn Transaction', 'id,status,date,branch,remarks,total', '(select a.*,b.name as branch from tbl_stockin_header a left join tbl_branch b on a.supplier_id=b.id ) tbl', 'id', 'stockin_refid,item_desc,qty,unit,cost,discount,total', 'tbl_stockin_items', 'stockin_refid', NULL, NULL);
INSERT INTO `tbl_master_details` (`id`, `title`, `column_master`, `tbl_master`, `primary_col_master`, `column_details`, `tbl_details`, `primary_col_details`, `nolimit`, `isgrouping`) VALUES (3, 'StockOut Transaction', 'id,status,date,branch,remarks,total', '(select a.*,b.name as branch from tbl_stockout_header a left join tbl_branch b on a.supplier_id=b.id ) tbl', 'id', 'stockin_refid,item_desc,qty,unit,cost,discount,total', 'tbl_stockout_items', 'stockin_refid', NULL, NULL);
INSERT INTO `tbl_master_details` (`id`, `title`, `column_master`, `tbl_master`, `primary_col_master`, `column_details`, `tbl_details`, `primary_col_details`, `nolimit`, `isgrouping`) VALUES (4, 'Inventory Audit', 'id,product_name,supplier_name,price,cost,inv_bal_forwarded,inv_po_receipts,inv_direct_purchases,inv_transfer_receipts,inv_sales_returns,inv_purchase_returns,inv_issuances,inv_sales,inv_adjustment,inv_transfer,ending_bal', '(select a.*,a.sku_id as id,b.supplier_name,c.price,c.cost,a.base_inv as ending_bal from tbl_product_name a,tbl_supplier b,tbl_barcodes c where a.supplier_id=b.id and a.sku_id=c.sku_id) tbl', 'id', NULL, NULL, NULL, 'yes', 'yes');


CREATE TABLE `tbl_branch` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=6
;

INSERT INTO `tbl_branch` (`id`, `name`) VALUES (1, 'ucbanilad');
INSERT INTO `tbl_branch` (`id`, `name`) VALUES (2, 'uclm');
INSERT INTO `tbl_branch` (`id`, `name`) VALUES (3, 'ucmain');
INSERT INTO `tbl_branch` (`id`, `name`) VALUES (4, 'ucmambaling');
INSERT INTO `tbl_branch` (`id`, `name`) VALUES (5, 'warehouse');

ALTER TABLE `tbl_product_name`
	ADD COLUMN `inv_bal_forwarded` DOUBLE NOT NULL DEFAULT '0' AFTER `tax_type`,
	ADD COLUMN `inv_po_receipts` DOUBLE NOT NULL DEFAULT '0' AFTER `inv_bal_forwarded`,
	ADD COLUMN `inv_direct_purchases` DOUBLE NOT NULL DEFAULT '0' AFTER `inv_po_receipts`,
	ADD COLUMN `inv_transfer_receipts` DOUBLE NOT NULL DEFAULT '0' AFTER `inv_direct_purchases`,
	ADD COLUMN `inv_sales_returns` DOUBLE NOT NULL DEFAULT '0' AFTER `inv_transfer_receipts`,
	ADD COLUMN `inv_purchase_returns` DOUBLE NOT NULL DEFAULT '0' AFTER `inv_sales_returns`,
	ADD COLUMN `inv_issuances` DOUBLE NOT NULL DEFAULT '0' AFTER `inv_purchase_returns`,
	ADD COLUMN `inv_sales` DOUBLE NOT NULL DEFAULT '0' AFTER `inv_issuances`,
	ADD COLUMN `inv_adjustment` DOUBLE NOT NULL DEFAULT '0' AFTER `inv_sales`,
	ADD COLUMN `inv_transfer` DOUBLE NOT NULL DEFAULT '0' AFTER `inv_adjustment`;

CREATE TABLE `tbl_stock_status` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`status_name` VARCHAR(50) NOT NULL DEFAULT '0',
	`type` VARCHAR(50) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=6
;
INSERT INTO `tbl_stock_status` (`id`, `status_name`, `type`) VALUES (3, 'Transfer Stock', 'stockout');
INSERT INTO `tbl_stock_status` (`id`, `status_name`, `type`) VALUES (4, 'Return Stock', 'stockout');
INSERT INTO `tbl_stock_status` (`id`, `status_name`, `type`) VALUES (1, 'Received from Supplier', 'stockin');
INSERT INTO `tbl_stock_status` (`id`, `status_name`, `type`) VALUES (2, 'Received from Branch', 'stockin');
INSERT INTO `tbl_stock_status` (`id`, `status_name`, `type`) VALUES (5, 'PO', 'po');

ALTER TABLE `tbl_stockout_header`
	ADD COLUMN `inv_tagging` INT NULL DEFAULT '0' AFTER `status`;
ALTER TABLE `tbl_stockin_header`
	ADD COLUMN `inv_tagging` INT NULL DEFAULT '0' AFTER `status`;	

CREATE TABLE `tbl_inv_tagging` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`tag_name` VARCHAR(50) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB
;

INSERT INTO `tbl_inv_tagging` (`id`, `tag_name`) VALUES (1, 'inv_bal_forwarded');
INSERT INTO `tbl_inv_tagging` (`id`, `tag_name`) VALUES (2, 'inv_po_receipts');
INSERT INTO `tbl_inv_tagging` (`id`, `tag_name`) VALUES (3, 'inv_direct_purchases');
INSERT INTO `tbl_inv_tagging` (`id`, `tag_name`) VALUES (4, 'inv_transfer_receipts');
INSERT INTO `tbl_inv_tagging` (`id`, `tag_name`) VALUES (5, 'inv_sales_returns');
INSERT INTO `tbl_inv_tagging` (`id`, `tag_name`) VALUES (6, 'inv_purchase_returns');
INSERT INTO `tbl_inv_tagging` (`id`, `tag_name`) VALUES (7, 'inv_adjustment');
INSERT INTO `tbl_inv_tagging` (`id`, `tag_name`) VALUES (8, 'inv_transfer');

CREATE TABLE `tbl_auto_gl_entry` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(50) NULL DEFAULT NULL,
	`dr_entry` TEXT NULL,
	`cr_entry` TEXT NULL,
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB
;
INSERT INTO `tbl_auto_gl_entry` (`id`, `title`, `dr_entry`, `cr_entry`) VALUES (1, 'Sales Short', '1012,1209', '4000');
INSERT INTO `tbl_auto_gl_entry` (`id`, `title`, `dr_entry`, `cr_entry`) VALUES (2, 'Sales Over', '1012', '4000,4000');
INSERT INTO `tbl_auto_gl_entry` (`id`, `title`, `dr_entry`, `cr_entry`) VALUES (3, 'Sales - ID Slings', '1012', '40001,2026');
INSERT INTO `tbl_auto_gl_entry` (`id`, `title`, `dr_entry`, `cr_entry`) VALUES (4, 'Sales - ID Slings Cost Entry', '5009', '1401');
INSERT INTO `tbl_auto_gl_entry` (`id`, `title`, `dr_entry`, `cr_entry`) VALUES (5, 'Sales - Books Cost Entry', '5000', '1400');
INSERT INTO `tbl_auto_gl_entry` (`id`, `title`, `dr_entry`, `cr_entry`) VALUES (6, 'Receiving of Books', '1400', '2000');
INSERT INTO `tbl_auto_gl_entry` (`id`, `title`, `dr_entry`, `cr_entry`) VALUES (7, 'Return of Books', '2000', '1400');
INSERT INTO `tbl_auto_gl_entry` (`id`, `title`, `dr_entry`, `cr_entry`) VALUES (8, 'Payment of Books', '2000', '5002,5005,1007');

ALTER TABLE `tbl_stockin_header`
	ADD COLUMN `glref` INT NOT NULL AFTER `total`;

ALTER TABLE `tbl_reading`
	ADD COLUMN `glref` INT NOT NULL AFTER `amount`;

ALTER TABLE `tbl_journal_entry`
	ADD COLUMN `center` VARCHAR(10) NULL DEFAULT NULL AFTER `cash_in_bank`;

ALTER TABLE `tbl_journal_entry`
	DROP INDEX `unique`,
	ADD UNIQUE INDEX `unique` (`refid`, `account_code`, `center`);

ALTER TABLE `tbl_journal_entry`
	ADD COLUMN `timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `cr`;

ALTER TABLE `tbl_vouchering`
	ADD COLUMN `center` VARCHAR(50) NULL DEFAULT NULL AFTER `id`,
	DROP COLUMN `center`,
	DROP INDEX `unique`,
	ADD UNIQUE INDEX `id_center` (`id`, `center`);

/*--changes in journal*/
CREATE TABLE `tbl_chart_of_account` (
	`account_code` INT(11) NOT NULL,
	`account_desc` VARCHAR(100) NULL DEFAULT NULL,
	`account_group` VARCHAR(20) NULL DEFAULT NULL,
	`account_type` VARCHAR(20) NULL DEFAULT NULL,
	`report_type` VARCHAR(20) NULL DEFAULT NULL,
	`default_side` VARCHAR(5) NULL DEFAULT NULL,
	`subsidiary` VARCHAR(5) NULL DEFAULT NULL,
	`cash_in_bank` VARCHAR(5) NULL DEFAULT NULL,
	UNIQUE INDEX `unique` (`account_code`, `account_type`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

CREATE TABLE `tbl_vouchering` (
	`id` INT(11) NOT NULL,
	`center` VARCHAR(50) NULL DEFAULT NULL,
	`date` DATE NULL DEFAULT NULL,
	`type` VARCHAR(50) NULL DEFAULT NULL,
	`status` VARCHAR(50) NULL DEFAULT NULL,
	`payee` VARCHAR(100) NULL DEFAULT NULL,
	`remarks` TEXT NULL,
	`particular_array` TEXT NULL,
	`amount_array` TEXT NULL,
	`total` DOUBLE NULL DEFAULT NULL,
	`preparedby` INT(11) NULL DEFAULT NULL,
	`certifiedcorrect` INT(11) NULL DEFAULT NULL,
	`approvedby` INT(11) NULL DEFAULT NULL,
	`notes` TEXT NULL,
	UNIQUE INDEX `id_center` (`id`, `center`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
CREATE TABLE `tbl_journal_entry` (
	`refid` INT(11) NULL DEFAULT NULL,
	`date` DATE NULL DEFAULT NULL,
	`account_code` VARCHAR(50) NULL DEFAULT NULL,
	`account_desc` VARCHAR(100) NULL DEFAULT NULL,
	`account_group` VARCHAR(20) NULL DEFAULT NULL,
	`account_type` VARCHAR(20) NULL DEFAULT NULL,
	`report_type` VARCHAR(10) NULL DEFAULT NULL,
	`subsidiary` VARCHAR(10) NULL DEFAULT NULL,
	`cash_in_bank` VARCHAR(10) NULL DEFAULT NULL,
	`center` VARCHAR(10) NULL DEFAULT NULL,
	`dr` DOUBLE NULL DEFAULT NULL,
	`cr` DOUBLE NULL DEFAULT NULL,
	`timestamp` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	UNIQUE INDEX `unique` (`refid`, `account_code`, `center`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

ALTER TABLE `tbl_vouchering`
	ADD COLUMN `datetime_changed` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `notes`;
ALTER TABLE `tbl_journal_entry`
	ADD COLUMN `datetime_changed` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `cr`;

ALTER TABLE `tbl_reading`
	ADD COLUMN `cashier_name` VARCHAR(100) NOT NULL AFTER `glref`;
	
CREATE TABLE `tbl_sales_voiditems` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`counter` INT(11) NOT NULL,
	`reading` INT(11) NOT NULL,
	`receipt` INT(11) NOT NULL,
	`item_array` TEXT NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;

CREATE TABLE `tbl_official_receipt_manual` (
	`or_num` INT(11) NULL DEFAULT NULL,
	`date` DATE NULL DEFAULT NULL,
	`customer_id` INT(11) NULL DEFAULT NULL,
	`amount` DOUBLE NULL DEFAULT NULL,
	`payment_of` VARCHAR(150) NULL DEFAULT NULL,
	`payment_type` VARCHAR(50) NULL DEFAULT NULL,
	`payment_details` VARCHAR(100) NULL DEFAULT NULL
)
ENGINE=InnoDB
;

ALTER TABLE `tbl_po_header`
	ADD COLUMN `glref` INT NOT NULL AFTER `datetime_changed`;

ALTER TABLE `tbl_po_header`
	ADD COLUMN `delivery_date` DATE NOT NULL AFTER `glref`;

ALTER TABLE `tbl_po_header`
	ADD COLUMN `rr_num` INT NOT NULL AFTER `datetime_changed`;
	
/* latest changes March 2017 */	
ALTER TABLE `tbl_stockout_items`
	ADD COLUMN `selling` DOUBLE NOT NULL AFTER `cost`;
ALTER TABLE `tbl_stockin_items`
	ADD COLUMN `selling` DOUBLE NOT NULL AFTER `cost`;
ALTER TABLE `tbl_po_items`
	ADD COLUMN `selling` DOUBLE NOT NULL AFTER `cost`;
ALTER TABLE `tbl_stocktransfer_items`
	ADD COLUMN `selling` DOUBLE NOT NULL AFTER `cost`;

CREATE ALGORITHM = UNDEFINED VIEW `view_receipt` AS SELECT * from tbl_sales_receipt_1
union
SELECT * from tbl_sales_receipt_2
union
SELECT * from tbl_sales_receipt_3
union
SELECT * from tbl_sales_receipt_4
union
SELECT * from tbl_sales_receipt_5 ;

ALTER TABLE `tbl_journal_entry`
	ADD COLUMN `ref_id` VARCHAR(50) NULL COMMENT 'can be used for supplier_id or other reference link' AFTER `datetime_changed`;

ALTER TABLE `tbl_sales_items`
	ADD COLUMN `category_id` INT NOT NULL AFTER `subjnametype`;

update tbl_sales_items a set category_id=(select category_id from tbl_sales_receipt_1 where receipt_id=a.receipt and reading=a.reading) where a.counter=1;
update tbl_sales_items a set category_id=(select category_id from tbl_sales_receipt_2 where receipt_id=a.receipt and reading=a.reading) where a.counter=2;
update tbl_sales_items a set category_id=(select category_id from tbl_sales_receipt_3 where receipt_id=a.receipt and reading=a.reading) where a.counter=3;
update tbl_sales_items a set category_id=(select category_id from tbl_sales_receipt_4 where receipt_id=a.receipt and reading=a.reading) where a.counter=4;
update tbl_sales_items a set category_id=(select category_id from tbl_sales_receipt_5 where receipt_id=a.receipt and reading=a.reading) where a.counter=5;

/* tbl_journal_entry check center ucmambalin */

/* 
	stockin.php line 273 $pricecost=$val['price'];
	pos_ajax_stockin.php line 177 "price"=>($row['selling']==0?$sku['price']:$row['selling']), //added new
*/

CREATE TABLE `tbl_floor_mapping` (
	`floor` VARCHAR(50) NOT NULL,
	`unit` VARCHAR(50) NOT NULL,
	`details` TEXT NOT NULL
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
INSERT INTO `tbl_floor_mapping` (`floor`, `unit`, `details`) VALUES ('3', '1', '2 BED ROOM UNIT\r\n- 63 square meters \r\n- inclusive of 1 Parking lot\r\n- Free water and electric connections.\r\n- Tiled floors, painted walls, Tiled toilets and complete fixtures, counter top cabinets, granite lavatory.');
INSERT INTO `tbl_floor_mapping` (`floor`, `unit`, `details`) VALUES ('3', '2', 'xcvxcv');
INSERT INTO `tbl_floor_mapping` (`floor`, `unit`, `details`) VALUES ('3', '3', 'dfgdfg');
INSERT INTO `tbl_floor_mapping` (`floor`, `unit`, `details`) VALUES ('4', '1', '');
INSERT INTO `tbl_floor_mapping` (`floor`, `unit`, `details`) VALUES ('4', '2', 'sdfsdf');
INSERT INTO `tbl_floor_mapping` (`floor`, `unit`, `details`) VALUES ('4', '3', '');

CREATE TABLE `tbl_journal_category` (
	`code` VARCHAR(10) NULL DEFAULT NULL,
	`description` VARCHAR(100) NULL DEFAULT NULL,
	`title` VARCHAR(100) NULL DEFAULT NULL,
	`type` VARCHAR(10) NULL DEFAULT NULL,
	UNIQUE INDEX `codeU` (`code`),
	INDEX `code` (`code`)
)
ENGINE=InnoDB
;


INSERT INTO `tbl_journal_category` (`code`, `description`, `title`, `type`) VALUES ('CDJ', 'CASH/CHECK DISBURSEMENT JOURNAL', 'CASH/CHECK DISBURSEMENT VOUCHER', 'CDJ');
INSERT INTO `tbl_journal_category` (`code`, `description`, `title`, `type`) VALUES ('CRJ', 'CASH/CHECK RECEIPT JOURNAL', 'CASH/CHECK RECEIPT', 'CRJ');
INSERT INTO `tbl_journal_category` (`code`, `description`, `title`, `type`) VALUES ('GJ', 'GENERAL JOURNAL', 'GENERAL VOUCHER', 'GJ');
INSERT INTO `tbl_journal_category` (`code`, `description`, `title`, `type`) VALUES ('PJ', 'PURCHASE JOURNAL', 'PURCHASE VOUCHER', 'PJ');
INSERT INTO `tbl_journal_category` (`code`, `description`, `title`, `type`) VALUES ('SJ', 'SALES JOURNAL', 'SALES VOUCHER', 'SJ');
INSERT INTO `tbl_journal_category` (`code`, `description`, `title`, `type`) VALUES ('TJ', 'TRANSITORY JOURNAL', '', 'TJ');

ALTER TABLE `tbl_vouchering`
	ADD COLUMN `typeinfo_array` TEXT NULL AFTER `notes`;

ALTER TABLE `tbl_customers_trans`
	DROP INDEX `refid`,
	ADD UNIQUE INDEX `refid` (`receipt`, `cust_id`, `transtype`);

CREATE TABLE `tbl_utilityreading_electricity` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`year` YEAR NOT NULL DEFAULT '2000',
	`month` INT(11) NOT NULL DEFAULT '0',
	`cust_id` INT(11) NOT NULL DEFAULT '0',
	`previous` DOUBLE NOT NULL DEFAULT '0',
	`present` DOUBLE NOT NULL DEFAULT '0',
	`reading` DOUBLE NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `year_month_cust_id` (`year`, `month`, `cust_id`)
)
ENGINE=InnoDB
;
CREATE TABLE `tbl_receipt_manual` (
	`receipt` INT(11) NULL DEFAULT NULL,
	`date` DATE NULL DEFAULT NULL,
	`cust_id` INT(11) NULL DEFAULT NULL,
	`cash_amt` DOUBLE NULL DEFAULT NULL,
	`check_amt` DOUBLE NULL DEFAULT NULL,
	`check_details` TEXT NULL,
	`check_date` DATE NULL DEFAULT NULL,
	`particular` TEXT NULL,
	UNIQUE INDEX `receipt` (`receipt`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;


ALTER TABLE `tbl_journal_entry`
	ADD COLUMN `fiscal_year` YEAR NULL DEFAULT '2017' AFTER `center`;
ALTER TABLE `tbl_journal_entry`
	ADD COLUMN `type` VARCHAR(10) NULL DEFAULT NULL AFTER `date`;
	
ALTER TABLE `tbl_journal_entry_ucbanilad`
	ADD COLUMN `fiscal_year` YEAR NULL DEFAULT '2017' AFTER `center`;
ALTER TABLE `tbl_journal_entry_ucbanilad`
	ADD COLUMN `type` VARCHAR(10) NULL DEFAULT NULL AFTER `date`;
ALTER TABLE `tbl_journal_entry_uclm`
	ADD COLUMN `fiscal_year` YEAR NULL DEFAULT '2017' AFTER `center`;
ALTER TABLE `tbl_journal_entry_uclm`
	ADD COLUMN `type` VARCHAR(10) NULL DEFAULT NULL AFTER `date`;
ALTER TABLE `tbl_journal_entry_ucmain`
	ADD COLUMN `fiscal_year` YEAR NULL DEFAULT '2017' AFTER `center`;
ALTER TABLE `tbl_journal_entry_ucmain`
	ADD COLUMN `type` VARCHAR(10) NULL DEFAULT NULL AFTER `date`;
ALTER TABLE `tbl_journal_entry_ucmambaling`
	ADD COLUMN `fiscal_year` YEAR NULL DEFAULT '2017' AFTER `center`;
ALTER TABLE `tbl_journal_entry_ucmambaling`
	ADD COLUMN `type` VARCHAR(10) NULL DEFAULT NULL AFTER `date`;
ALTER TABLE `tbl_journal_entry_warehouse`
	ADD COLUMN `fiscal_year` YEAR NULL DEFAULT '2017' AFTER `center`;
ALTER TABLE `tbl_journal_entry_warehouse`
	ADD COLUMN `type` VARCHAR(10) NULL DEFAULT NULL AFTER `date`;

/*CREATE TABLE `tbl_journal_cashinbank` (
	`refid` INT(11) NULL DEFAULT NULL,
	`date` DATE NULL DEFAULT NULL,
	`check_number` VARCHAR(100) NULL DEFAULT NULL,
	`bank_validation` VARCHAR(100) NULL DEFAULT NULL,
	`dr` DOUBLE NULL DEFAULT NULL,
	`cr` DOUBLE NULL DEFAULT NULL,
	`cleared` INT(11) NULL DEFAULT '0' COMMENT '1-clear'
)
ENGINE=InnoDB
;*/
ALTER TABLE `tbl_journal_entry`
	ADD COLUMN `check_date` DATE NULL DEFAULT NULL AFTER `ref_id`,
	ADD COLUMN `check_number` VARCHAR(100) NULL DEFAULT NULL AFTER `check_date`,
	ADD COLUMN `bank` VARCHAR(100) NULL DEFAULT NULL AFTER `check_number`,
	ADD COLUMN `bank_validation` VARCHAR(100) NULL DEFAULT NULL AFTER `bank`;
	
ALTER TABLE `tbl_journal_entry`
	ADD COLUMN `payto` VARCHAR(100) NULL DEFAULT NULL AFTER `ref_id`;

ALTER TABLE `tbl_journal_entry`
	ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT FIRST,
	ADD PRIMARY KEY (`id`);

ALTER TABLE `tbl_journal_entry`
	ADD COLUMN `ar_refid` INT NULL DEFAULT NULL AFTER `bank_validation`,
	ADD COLUMN `ap_refid` INT NULL DEFAULT NULL AFTER `ar_refid`;

/*---------------------*/
ALTER TABLE `tbl_journal_entry`
	DROP INDEX `unique`,
	ADD UNIQUE INDEX `unique` (`refid`, `account_code`, `center`, `type`);
ALTER TABLE `tbl_vouchering`
	ALTER `id` DROP DEFAULT;
ALTER TABLE `tbl_vouchering`
	CHANGE COLUMN `id` `id` INT(11) NOT NULL FIRST,
	DROP INDEX `id_center`,
	ADD UNIQUE INDEX `id_type` (`id`, `type`),
	ADD PRIMARY KEY (`id`);

ALTER TABLE `tbl_vouchering`
	ADD COLUMN `reference` TEXT NULL AFTER `remarks`;
	
CREATE TABLE `tbl_group_sorting` (
	`id` INT(11) NULL DEFAULT NULL,
	`group_name` VARCHAR(50) NULL DEFAULT NULL
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
INSERT INTO `tbl_group_sorting` (`id`, `group_name`) VALUES (1, 'SALES');
INSERT INTO `tbl_group_sorting` (`id`, `group_name`) VALUES (3, 'EXPENSES');
INSERT INTO `tbl_group_sorting` (`id`, `group_name`) VALUES (2, 'COST OF SALES');

ALTER TABLE `tbl_master_details`
	ADD COLUMN `default_template` TEXT NULL DEFAULT NULL AFTER `title`;	
	
INSERT INTO `tbl_master_details` (`id`, `title`, `default_template`, `column_master`, `tbl_master`, `primary_col_master`, `column_details`, `tbl_details`, `primary_col_details`, `nolimit`, `isgrouping`) VALUES (6, 'Ledger', NULL, 'account_code,date,account_desc,type,remarks,reference', '(select a.*,b.remarks,b.reference from tbl_journal_entry a left join tbl_vouchering b on a.refid=b.id and a.type=b.type) tbl', 'refid|type', 'refid,date,account_code,account_desc,check_date,check_number,bank,dr,cr', 'tbl_journal_entry', 'refid|type', '', '');

/*-----------------*/

CREATE TABLE `settings` (
	`variable_name` VARCHAR(50) NULL DEFAULT NULL,
	`variable_values` VARCHAR(1000) NULL DEFAULT NULL
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('mode', 'branch');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('allow_price_below_cost', 'false');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('allow_negative_inv', 'false');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('session_connect', 'ucmambaling');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('192.168.10.87', 'counter_num:1,serial:6VMGVQGQ,permit:0411-082-95150-002,machine:110222710');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('192.168.10.226', 'counter_num:2,serial:WCC2EV083555,permit:0314-082-182451-002,machine:140349156');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('192.168.10.225', 'counter_num:3,serial:6VMEJ52P,permit:0411-082-95149-002,machine:110222709');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('192.168.10.224', 'counter_num:4,serial:6VMGVQNH,permit:0411-082-95148-002,machine:110222708');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('192.168.1.12', '4,6VMGVQNH,0411-082-95148-002,110222708');

update tbl_sales_items set vat=(selling/9.333) where category_id=2
/*-----------------*/
CREATE TABLE `tbl_subject_name` (
	`code` VARCHAR(50) NULL DEFAULT NULL,
	`subject_name` VARCHAR(100) NULL DEFAULT NULL,
	`school_level` VARCHAR(50) NULL DEFAULT NULL,
	UNIQUE INDEX `code` (`code`)
)
ENGINE=InnoDB
;

ALTER TABLE `tbl_product_name`
	ADD COLUMN `hidden` VARCHAR(50) NULL DEFAULT NULL AFTER `inv_transfer`;
	
ALTER TABLE `tbl_receipt_manual`
	ADD COLUMN `deposited_date` DATE NULL AFTER `particular`;

create view
select a.*,b.reference,b.remarks,b.total,b.center centername from tbl_journal_entry a left join tbl_vouchering b on a.refid=b.id order by refid,dr desc 

ALTER TABLE `tbl_receipt_manual`
	ADD COLUMN `rent_amount` DOUBLE NULL DEFAULT NULL AFTER `deposited_date`,
	ADD COLUMN `other_amount` DOUBLE NULL DEFAULT NULL AFTER `rent_amount`,
	ADD COLUMN `total_amount` DOUBLE NULL DEFAULT NULL AFTER `other_amount`;

ALTER TABLE `tbl_receipt_manual`
	ADD COLUMN `receivedfrom` VARCHAR(100) NULL DEFAULT NULL AFTER `cust_id`,
	ADD COLUMN `paymentof` VARCHAR(100) NULL DEFAULT NULL AFTER `receivedfrom`;

CREATE TABLE `tbl_customers_rent_amt` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`cust_id` INT(11) NULL DEFAULT NULL,
	`contract_start` DATE NULL DEFAULT NULL,
	`contract_end` DATE NULL DEFAULT NULL,
	`rent_amount` DOUBLE NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB
;

CREATE TABLE `settings_connections` (
	`con_name` VARCHAR(50) NULL DEFAULT NULL,
	`db_name` VARCHAR(50) NULL DEFAULT NULL,
	`ipaddress` VARCHAR(50) NULL DEFAULT NULL
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('mode', 'main');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('allow_price_below_cost', 'false');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('allow_negative_inv', 'true');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('session_connect', 'emall');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('192.168.10.87', 'counter_num:1,serial:6VMGVQGQ,permit:0411-082-95150-002,machine:110222710');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('192.168.10.226', 'counter_num:2,serial:WCC2EV083555,permit:0314-082-182451-002,machine:140349156');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('192.168.10.225', 'counter_num:3,serial:6VMEJ52P,permit:0411-082-95149-002,machine:110222709');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('192.168.10.224', 'counter_num:4,serial:6VMGVQNH,permit:0411-082-95148-002,machine:110222708');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('192.168.1.12', '4,6VMGVQNH,0411-082-95148-002,110222708');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('howmanycounter', '4');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('connection_type', 'multiple');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('view', '6');


INSERT INTO `settings_connections` (`con_name`, `db_name`, `ipaddress`) VALUES ('uclm', 'lizgan_uclm', '192.168.10.138');
INSERT INTO `settings_connections` (`con_name`, `db_name`, `ipaddress`) VALUES ('ucmambaling', 'lizgan_ucmambaling', '192.168.10.42');
INSERT INTO `settings_connections` (`con_name`, `db_name`, `ipaddress`) VALUES ('ucmain', 'lizgan_ucmain', '192.168.10.85');
INSERT INTO `settings_connections` (`con_name`, `db_name`, `ipaddress`) VALUES ('ucbanilad', 'lizgan_ucbanilad', '192.168.10.176');
INSERT INTO `settings_connections` (`con_name`, `db_name`, `ipaddress`) VALUES ('warehouse', 'lizgan_warehouse', '192.168.10.57');

ALTER TABLE `tbl_soa`
	ADD COLUMN `year` YEAR NOT NULL DEFAULT '0' AFTER `id`,
	ADD COLUMN `month` INT(11) NOT NULL DEFAULT '0' AFTER `year`;
	
ALTER TABLE `tbl_soa_current_charges`
	ADD COLUMN `year` YEAR NOT NULL DEFAULT '0' AFTER `id`,
	ADD COLUMN `month` INT(11) NOT NULL DEFAULT '0' AFTER `year`;
ALTER TABLE `tbl_soa_other_charges`
	ADD COLUMN `year` YEAR NOT NULL DEFAULT '0' AFTER `id`,
	ADD COLUMN `month` INT(11) NOT NULL DEFAULT '0' AFTER `year`;
ALTER TABLE `tbl_soa_reimbursable_charges`
	ADD COLUMN `year` YEAR NOT NULL DEFAULT '0' AFTER `id`,
	ADD COLUMN `month` INT(11) NOT NULL DEFAULT '0' AFTER `year`;

CREATE TABLE `tbl_employee` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`first_name` VARCHAR(50) NULL DEFAULT '0',
	`last_name` VARCHAR(50) NULL DEFAULT '0',
	`middle_name` VARCHAR(50) NULL DEFAULT '0',
	`daily_rate` DOUBLE NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB
;
CREATE TABLE `tbl_payroll_dtr` (
	`empid` INT(11) NULL DEFAULT NULL,
	`date` DATE NULL DEFAULT NULL,
	`type` VARCHAR(50) NULL DEFAULT NULL,
	`status` VARCHAR(50) NULL DEFAULT NULL,
	`reghours` DOUBLE NULL DEFAULT NULL,
	`regtotal` DOUBLE NULL DEFAULT NULL,
	`othours` DOUBLE NULL DEFAULT NULL,
	`otamt` DOUBLE NULL DEFAULT NULL,
	`subtotal` DOUBLE NULL DEFAULT NULL,
	UNIQUE INDEX `empid_date` (`empid`, `date`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

CREATE TABLE `tbl_payroll_header` (
	`id` INT(11) NOT NULL,
	`begdate` DATE NULL DEFAULT NULL,
	`enddate` DATE NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB
;
CREATE TABLE `tbl_payroll_deduction` (
	`id` INT(11) NULL DEFAULT NULL,
	`payrollid` INT(11) NULL DEFAULT NULL,
	`empid` INT(11) NULL DEFAULT NULL,
	`details` VARCHAR(100) NULL DEFAULT NULL,
	`amount` DOUBLE NULL DEFAULT NULL
)
ENGINE=InnoDB
;
CREATE TABLE `tbl_payroll_advances` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`empid` INT(11) NULL DEFAULT NULL,
	`details` VARCHAR(100) NULL DEFAULT NULL,
	`amount` DOUBLE NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=10
;


INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('connection_type', 'multiple');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('view', '5');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('buttons', 'lizgan');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('system_name', 'Lizgan System');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('system_fullname', 'LIZGAN DISTRIBUTORS, INC.');

INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('otreg', '1.25');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('wod', '1.30');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('special_hol', '1.30');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('legal_hol', '2');
INSERT INTO `settings` (`variable_name`, `variable_values`) VALUES ('postype', 'single');

CREATE TABLE `tbl_soa_other_charges_category_name` (
	`category_id` INT(11) NOT NULL AUTO_INCREMENT,
	`Description` VARCHAR(100) NULL DEFAULT NULL,
	`rate` DOUBLE NULL DEFAULT NULL,
	`unit` VARCHAR(10) NULL DEFAULT NULL,
	PRIMARY KEY (`category_id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
CREATE TABLE `tbl_soa_other_charges_daily` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`custid` INT(11) NULL DEFAULT NULL,
	`forthemonth` VARCHAR(50) NULL DEFAULT NULL,
	`category_id` INT(11) NULL DEFAULT NULL,
	`date` DATE NULL DEFAULT NULL,
	`reference` VARCHAR(100) NULL DEFAULT NULL,
	`qty` DOUBLE NULL DEFAULT NULL,
	`unit` VARCHAR(20) NULL DEFAULT NULL,
	`rate` DOUBLE NULL DEFAULT NULL,
	`amount` DOUBLE NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
CREATE TABLE `tbl_soa_pdclist` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`custid` INT(11) NULL DEFAULT NULL,
	`monthrent` VARCHAR(50) NULL DEFAULT NULL,
	`checkdate` DATE NULL DEFAULT NULL,
	`bank` VARCHAR(100) NULL DEFAULT NULL,
	`checknum` VARCHAR(50) NULL DEFAULT NULL,
	`amount` DOUBLE NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;

CREATE TABLE `tbl_customers_contract` (
	`contract_number` INT(11) NOT NULL AUTO_INCREMENT,
	`custid` INT(11) NULL DEFAULT NULL,
	`contract_date` DATE NULL DEFAULT NULL,
	`contract_start_date` DATE NULL DEFAULT NULL,
	`contract_end_date` DATE NULL DEFAULT NULL,
	`start_bill_period` DATE NULL DEFAULT NULL,
	`fixed_monthly_rental` DOUBLE NULL DEFAULT NULL,
	`min_monthly_rental` DOUBLE NULL DEFAULT NULL,
	`escalation_rate` DOUBLE NULL DEFAULT NULL,
	`num_years` DOUBLE NULL DEFAULT NULL,
	`insurance_company` VARCHAR(100) NULL DEFAULT NULL,
	`actual_open_date` DATE NULL DEFAULT NULL,
	`security_deposit` DOUBLE NULL DEFAULT NULL,
	`electrical_deposit` DOUBLE NULL DEFAULT NULL,
	`adv_rental` DOUBLE NULL DEFAULT NULL,
	`withholding_tax` DOUBLE NULL DEFAULT NULL,
	`vat` DOUBLE NULL DEFAULT NULL,
	`discount` DOUBLE NULL DEFAULT NULL,
	`remarks` TEXT NULL,
	`elect_mincharge` DOUBLE NULL DEFAULT NULL,
	`elect_rate` DOUBLE NULL DEFAULT NULL,
	`water_mincharge` DOUBLE NULL DEFAULT NULL,
	`water_rate` DOUBLE NULL DEFAULT NULL,
	`others_cusa_rate` DOUBLE NULL DEFAULT NULL,
	`others_aircon_rate` DOUBLE NULL DEFAULT NULL,
	`others_pestcontrol_percent` DOUBLE NULL DEFAULT NULL,
	`others_percent_ingross_sales` DOUBLE NULL DEFAULT NULL,
	PRIMARY KEY (`contract_number`),
	UNIQUE INDEX `custid` (`custid`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
CREATE TABLE `tbl_customers_contract_recurring_charges` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`custid` INT(11) NULL DEFAULT NULL,
	`charge_description` VARCHAR(100) NULL DEFAULT NULL,
	`amount` DOUBLE NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;


CREATE TABLE `tbl_mall_unit` (
	`id` INT(11) NULL DEFAULT NULL,
	`mall_unit_number` VARCHAR(100) NULL DEFAULT NULL,
	`floor_area_sqm` DOUBLE NULL DEFAULT NULL,
	`fixed_rental_per_sqm` DOUBLE NULL DEFAULT NULL,
	`fixed_rental_amount` DOUBLE NULL DEFAULT NULL
)
ENGINE=InnoDB
;

ALTER TABLE `tbl_supplier`
	ADD COLUMN `address` VARCHAR(200) NOT NULL AFTER `supplier_name`,
	ADD COLUMN `contact_number` VARCHAR(100) NOT NULL AFTER `address`,
	ADD COLUMN `mobile_number` VARCHAR(100) NOT NULL AFTER `contact_number`,
	ADD COLUMN `fax_number` VARCHAR(100) NOT NULL AFTER `mobile_number`,
	ADD COLUMN `email_add` VARCHAR(100) NOT NULL AFTER `fax_number`,
	ADD COLUMN `contact_person` VARCHAR(100) NOT NULL AFTER `email_add`,
	ADD COLUMN `owner` VARCHAR(100) NOT NULL AFTER `contact_person`;

CREATE TABLE `tbl_quotation_header` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`cust_id` INT(11) NOT NULL DEFAULT '0',
	`date` DATE NOT NULL,
	`validity_of_offer` VARCHAR(100) NOT NULL DEFAULT '0',
	`payment_terms` VARCHAR(100) NOT NULL DEFAULT '0',
	`delivery` VARCHAR(100) NOT NULL DEFAULT '0',
	`rfqnum` VARCHAR(100) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=6
;
CREATE TABLE `tbl_quotation_items` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`refid` INT(11) NULL DEFAULT NULL,
	`item_spec` VARCHAR(100) NULL DEFAULT NULL,
	`qty` DOUBLE NULL DEFAULT NULL,
	`unit` VARCHAR(50) NULL DEFAULT NULL,
	`unitprice` DOUBLE NULL DEFAULT NULL,
	`amount` DOUBLE NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=12
;
ALTER TABLE `tbl_customers`
	ADD COLUMN `email_add` VARCHAR(50) NOT NULL AFTER `tin`,
	ADD COLUMN `contact_number` VARCHAR(50) NOT NULL AFTER `email_add`,
	ADD COLUMN `mobile_number` VARCHAR(50) NOT NULL AFTER `contact_number`,
	ADD COLUMN `fax_number` VARCHAR(50) NOT NULL AFTER `mobile_number`;

CREATE TABLE `tbl_soa_nonvat` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`year` YEAR NULL DEFAULT '2000',
	`month` INT(11) NULL DEFAULT '0',
	`cust_id` INT(11) NULL DEFAULT '0',
	`date` DATE NULL DEFAULT NULL,
	`details` VARCHAR(150) NULL DEFAULT '0',
	`amount` DOUBLE NULL DEFAULT '0',
	`class` VARCHAR(50) NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

ALTER TABLE `tbl_chart_of_account`
	ADD COLUMN `sub_account` VARCHAR(100) NULL DEFAULT NULL AFTER `account_desc`,
	ADD COLUMN `sub_account_group` VARCHAR(100) NULL DEFAULT NULL AFTER `sub_account`;

CREATE TABLE `tbl_chart_of_account_subaccount` (
	`code` INT(11) NOT NULL AUTO_INCREMENT,
	`sub_account` VARCHAR(100) NOT NULL DEFAULT '0',
	`sub_account_group` VARCHAR(100) NOT NULL DEFAULT '0',
	PRIMARY KEY (`code`)
)
COMMENT='linked to sub_account field'
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
CREATE TABLE `tbl_chart_of_account_sub_account_group` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`sub_account_group` VARCHAR(100) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB
;


update tbl_journal_entry a set sub_account=(select sub_account from tbl_chart_of_account where account_code=a.account_code),sub_account_group=(select sub_account_group from tbl_chart_of_account where account_code=a.account_code)

CREATE TABLE `tbl_billing_statement_header` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`cust_id` INT(11) NOT NULL DEFAULT '0',
	`date` DATE NOT NULL,
	`ponum` VARCHAR(100) NOT NULL DEFAULT '0',
	`payment_terms` VARCHAR(100) NOT NULL DEFAULT '0',
	`regnum` VARCHAR(100) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
CREATE TABLE `tbl_billing_statement_items` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`refid` INT(11) NULL DEFAULT NULL,
	`item_spec` VARCHAR(100) NULL DEFAULT NULL,
	`qty` DOUBLE NULL DEFAULT NULL,
	`unit` VARCHAR(50) NULL DEFAULT NULL,
	`unitprice` DOUBLE NULL DEFAULT NULL,
	`amount` DOUBLE NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
CREATE TABLE `tbl_sales_invoice_header` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`cust_id` INT(11) NOT NULL DEFAULT '0',
	`date` DATE NOT NULL,
	`ponum` VARCHAR(100) NOT NULL DEFAULT '0',
	`payment_terms` VARCHAR(100) NOT NULL DEFAULT '0',
	`regnum` VARCHAR(100) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
CREATE TABLE `tbl_sales_invoice_items` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`refid` INT(11) NULL DEFAULT NULL,
	`item_spec` VARCHAR(100) NULL DEFAULT NULL,
	`qty` DOUBLE NULL DEFAULT NULL,
	`unit` VARCHAR(50) NULL DEFAULT NULL,
	`unitprice` DOUBLE NULL DEFAULT NULL,
	`amount` DOUBLE NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
ALTER TABLE `tbl_product_name`
	ADD COLUMN `group_id` INT(11) NULL DEFAULT NULL AFTER `category_id`;
ALTER TABLE `tbl_product_name`
	CHANGE COLUMN `group_id` `group_id` INT(11) NULL DEFAULT NULL COMMENT 'left join tbl_prod_group on tbl_product_name.group_id=tbl_prod_group.id|group_name|tbl_prod_group' AFTER `category_id`;
CREATE TABLE `tbl_prod_group` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`group_name` VARCHAR(50) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB
;
update tbl_vouchering a, (select @n:=9611)	b set a.`notes`=a.id,a.id=(@n:=@n+1) where a.`type`='CRJ' and date>'2017-07-31';
update tbl_journal_entry a set refid=(select b.id from tbl_vouchering b where b.notes=a.refid and b.`type`='CRJ' limit 1) where a.`type`='CRJ' AND a.`date`>'2017-07-31';

//backward updating
update tbl_vouchering a
join
(select c.*,@n:=@n-1 num from (select @n:=3389) b cross join (select * from tbl_vouchering order by datetime_changed desc) c) tmp
on a.id=tmp.id 
set a.id=(tmp.num+100000) where a.`type`='CDJ' and a.date<='2017-07-31';

--------------
update tbl_vouchering a
join
(select c.*,@n:=@n+1 num from (select @n:=9611) b cross join (select * from tbl_vouchering where `type`='CRJ' and date>'2017-07-1' order by date asc) c) tmp
on a.id=tmp.id 
set a.id=tmp.num;

CREATE TABLE `tbl_seriesnum` (
	`type` VARCHAR(50) NULL DEFAULT NULL,
	`seriesnum` VARCHAR(50) NULL DEFAULT '0'
)
ENGINE=InnoDB
;


//BACKWARD
update tbl_vouchering a
join
(select c.*,@n:=@n-1 num from (select @n:=9614) b cross join (select * from tbl_vouchering where `type`='CRJ' and date<='2017-07-31' order by date desc) c) tmp
on a.id=tmp.id 
set a.id=((tmp.num*1)+100000) where a.`type`='CRJ' and a.date<='2017-07-31';

//FORWARD
update tbl_vouchering a
join
(select c.*,@n:=@n+1 num from (select @n:=9613) b cross join (select * from tbl_vouchering where `type`='CRJ' and date>'2017-07-31' order by date asc) c) tmp
on a.id=tmp.id 
set a.id=((tmp.num*1)) where a.`type`='CRJ' and a.date>'2017-07-31';

//ENTRY UPDATE
update tbl_journal_entry a set refid=(select b.id from tbl_vouchering b where b.notes=a.refid and b.`type`='CRJ' limit 1) where a.`type`='CRJ';


//changes in journal entry table
ALTER TABLE `tbl_journal_entry`
	DROP INDEX `unique`;

//Laboratory Program
CREATE TABLE `lab_exam_format` (
	`skuid` INT(11) NULL DEFAULT NULL,
	`fields_label` TEXT NULL,
	`default_value` TEXT NULL,
	`unit_label` TEXT NULL,
	`reference_range` TEXT NULL
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
INSERT INTO `lab_exam_format` VALUES (1, 'Red Blood Cell|Hematocrit|Hemoglobin|Mean Corpuscular Vol.|Mean Corpuscular Hgb.|Mean Corpuscular Hgb. Conc.|Red Cell Distribution Width|White Blood Cell|Stab|Neutrophils|Lymphocytes|Monocytes|Eosinophils|Basophils|Platelet Count|Mean Platelet Vol.|Blood Typing|Rh Typing', '||||||||||||||||A+,B+|Positive,Negative', 'x10<sup>12</sup>/L|%|g/L|fL|pg|g/L|%|x10<sup>9</sup>/L|%|%|%|%|%|%|x10<sup>9</sup>/L|fL', '4.40 - 5.80|42.0-52.0|130.0-180.0|80.0-100.0|26.0-34-0|310.0 - 360.0|11.2 - 16.1|5.0 - 10.0|0.00 - 4.00|45.0 - 70.0|20.0 - 40.0|2.0 - 8.0|0.00 - 4.00|0.00 - 2.00|150.0 - 450.0|6.5 - 12.0');
ALTER TABLE `tbl_sales_receipt_1`
	ADD COLUMN `tblsource` VARCHAR(50) NOT NULL AFTER `timestamp`;
	
ALTER TABLE `tbl_po_header`
	ADD COLUMN `attention` VARCHAR(100) NOT NULL AFTER `supplier_id`;

CREATE TABLE `settings_reports` (
	`report_name` VARCHAR(50) NULL DEFAULT NULL,
	`report_link` VARCHAR(250) NULL DEFAULT NULL
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;
INSERT INTO `settings_reports` (`report_name`, `report_link`) VALUES ('PO', '../reports/po_rber.php');


//1-24-18
tbl_vouchering change unique add type


CREATE TABLE `tbl_cost_center` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`cost_center` VARCHAR(100) NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB
;


tbl_journal_entry change unique add type

select cust_id,datediff(now(),date) daysnum,sum(amount) bal from tbl_customers_trans group by cust_id,datediff(now(),date)

//April 01, 2018

ALTER TABLE `tbl_customers_trans`
	CHANGE COLUMN `receipt` `receipt` VARCHAR(50) NOT NULL AFTER `cust_id`;
	
ALTER TABLE `tbl_customers_trans`
	ADD COLUMN `or_ref` VARCHAR(50) NOT NULL AFTER `reading`;
	
ALTER TABLE `tbl_customers`
	ADD COLUMN `agent_id` VARCHAR(50) NOT NULL AFTER `fax`;
	
	
ALTER TABLE `tbl_customers_trans`
	ADD COLUMN `more_details` TEXT NOT NULL AFTER `details`;
	
ALTER TABLE `tbl_journal_entry`
	ADD COLUMN `ar_nontrade_refid` INT(11) NULL DEFAULT NULL AFTER `ap_refid`;
	
ALTER TABLE `tbl_sales_invoice_header`
	ADD COLUMN `cust_address` VARCHAR(100) NOT NULL AFTER `preparedby`,
	ADD COLUMN `cust_telnum` VARCHAR(100) NOT NULL AFTER `cust_address`,
	ADD COLUMN `cust_faxnum` VARCHAR(100) NOT NULL AFTER `cust_telnum`,
	ADD COLUMN `cust_contactperson` VARCHAR(100) NOT NULL AFTER `cust_faxnum`,
	ADD COLUMN `cust_tin` VARCHAR(100) NOT NULL AFTER `cust_contactperson`;

