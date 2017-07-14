<?php

/**
 * 外卖配送接口类
 * Created by PhpStorm.
 * User: jobinlin
 * Date: 2017/5/3
 * Time: 10:13
 */
interface TakeDelivery
{
    /**
     * 发布订单到第三方平台
     * @param unknown $order_id
     * @param number $type $type=0发布订单,$type=1 订单被取消、过期或者投递异常的情况下，调用此接口，可以在达达平台重新发布订单。
     */
    public function sendOrder($order_id);

    /**
     * 订单回调
     * @return mixed
     */
    public function callbackOrder();

    /**
     * 查询订单状态
     * @return mixed
     */
    public function queryOrder($order_id);

    /**
     * 取消订单
     * @return mixed
     */
    public function cancelOrder($order_id,$cancel_reason);

    /**
     * 查询配送人员状态
     * @return mixed
     */
    public function carrierOrder($order_id);

    /**
     * 创建商户
     * @param $data
     * @return mixed
     */
    public function createSupplier($supplier_id);

    /**
     * 创建门店
     * @param $data
     * @return mixed
     */
    public function createStore($location_id);

    /**
     * 更新门店
     * @param $data
     * @return mixed
     */
    public function updateStore($location_id);


}