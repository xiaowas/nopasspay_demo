<?php
//接收异步通知请求demo文件
//签名算法库
include('sign.php');

//商户名称
$account_name = $_POST['account_name'];
//支付时间戳
$pay_time = $_POST['pay_time'];
//支付状态
$status = $_POST['status'];
//支付金额
$amount = $_POST['amount'];
//支付时提交的订单信息
$out_trade_no = $_POST['out_trade_no'];
//平台订单交易流水号
$trade_no = $_POST['trade_no'];
//该笔交易手续费用
$fees = $_POST['fees'];
//签名算法
$sign = $_POST['sign'];
//回调时间戳
$callback_time = $_POST['callback_time'];
//支付类型
$type = $_POST['type'];
//商户KEY（S_KEY）
$account_key = $_POST['account_key'];


//第一步，检测商户KEY是否一致
if ($account_key != '你的商户KEY') exit('error:key');
//第二步，验证签名是否一致
if (sign('你的商户KEY', ['amount' => $amount, 'out_trade_no' => $out_trade_no]) != $sign) exit('error:sign');

//下面就可以安全的使用上面的信息给贵公司平台进行入款操作

$servername = "localhost";
$username = "demo7_qqfe_xin";
$password = "qwe12222";
$dbname = "demo7_qqfe_xin";

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);
// 检测连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}
$curr_time = time();
$curr_date = date('Y-m-d H:i:s', $curr_time);
$sql = "INSERT INTO codepay_order (`pay_id`, `money`, `price`, `type`, `pay_no`, `param`, `pay_time`, `pay_tag`, `status`, `creat_time`, `up_time`)
VALUES ('" . $extend . "', '" . $realprice . "', '" . $price . "',  '" . $type . "', '" . $out_order_id . "', '" . $extend . "', '" . $paytime . "', '', '1', '" . $curr_time . "', '" . $curr_date . "')";

if ($conn->query($sql) === TRUE) {
    //echo "新记录插入成功";
    mysqli_query($conn, "UPDATE zh_member SET money=money+{$realprice} WHERE username='" . $extend . "'");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}


$conn->close();

//测试时，将来源请求写入到txt文件，方便分析查看
file_put_contents("callback_log.txt", json_encode($_POST));