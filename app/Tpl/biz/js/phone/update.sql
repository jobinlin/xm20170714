ALTER TABLE `fanwe_deal`
ADD COLUMN `phone_description`  text NOT NULL COMMENT '手机端描述';
ALTER TABLE `fanwe_deal_submit`
ADD COLUMN `phone_description`  text NOT NULL COMMENT '手机端描述';

