<?php
require 'public/staff-inventory.php';
function addToCart(){
    require 'public/connection.php';
    if (!empty($_GET['action'])) {
        switch ($_GET['action']) {
            case "add":
                if (!empty($_POST["quantity"])) {
                    $id = $_GET['code'];
                    $getProduct = $connect->prepare("SELECT * FROM tblproducts WHERE code=?");
                    $getProduct->bind_param('s', $id);
                    $getProduct->execute();
                    $row = $getProduct->get_result();
                    while ($fetch = $row->fetch_array()) {
                        $itemArray = array($fetch["code"] => array(
                            'name' => $fetch["productName"],
                            'id' => $fetch["code"],
                            'name' => $fetch["productName"],
                            'productCode' => $fetch["code"],
                            'category' => $fetch["productCategory"],
                            'quantity' => $_POST["quantity"],
                            'variation' => $fetch["productVariation"],
                            'price' => $fetch["price"]
                        ));
                        if (!empty($_SESSION["cart"])) {
                            if (in_array($fetch["code"], array_keys($_SESSION["cart"]))) {
                                foreach ($_SESSION["cart"] as $k => $v) {
                                    if ($fetch["code"] == $k) {
                                        if (empty($_SESSION["cart"][$k]["quantity"])) {
                                            $_SESSION["cart"][$k]["quantity"] = 0;
                                        }
                                        $_SESSION["cart"][$k]["quantity"] += $_POST["quantity"];
                                        header('Location:pos.php');
                                    }
                                }
                            } else {
                                $_SESSION["cart"] = array_merge($_SESSION["cart"], $itemArray);
                            }
                        } else {
                            $_SESSION["cart"] = $itemArray;
                            header('Location:pos.php');
                        }
                    }
                }
    
                break;
            case "remove":
                if (!empty($_SESSION["cart"])) {
                    foreach ($_SESSION["cart"] as $k => $v) {
                        if ($_GET["code"] == $k) {
                            unset($_SESSION["cart"][$k]);
                        }
                        if (empty($_SESSION["cart"])) {
                            unset($_SESSION["cart"]);
                        }
                    }
                }
                break;
            case "empty":
                unset($_SESSION["cart"]);
                break;
        }    
    }
}


function insertCart(){
    require 'public/connection.php';
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        date_default_timezone_set('Asia/Manila');
        if (isset($_POST['btn-save-cart'])) {
            $id = $_POST['id'];
            $posId = "";
            $orderedDate = date('y-m-d');
            $quantity = $_POST['quantity'];
            $productName = $_POST['productName'];
            $variation =  $_POST['variation'];
            $price = $_POST['price'];
            $subtotal = $_POST['subTotal'];
            $total = $_POST['totalPrice'];
            $productCode = $_POST['productCode'];
            $category = $_POST['productCategory'];
            $status = "POS";
            $fname = " ";
            $lname = " ";
            $amountPay = $_POST['amountPay'];
            $returnChange = $_POST['returnChange'];
            $selectedCustomer = $_POST['selectedCustomer'];
            $noSelectedCustomer="";
            $discount = $_POST['discount'];
            $pwdSeniorNumber = $_POST['idNumber'];
            $dateCode = date('Ymd');
            $bindHexCode =  bin2hex(openssl_random_pseudo_bytes(5));
            $noIdNumber = $dateCode.$bindHexCode;
            $discountedPrice = $_POST['discountedPrice'];
            $noDiscount = "";
            $notPwdSenior = "";
            $status = "Processing";
          
           foreach ($id as $index => $code) {
                $ids = $code;
                $ordered_date = $orderedDate[$index];
                $s_quantity = $quantity[$index];
                $s_productName = $productName[$index];
                $s_variation = $variation[$index];
                $s_price = $price[$index];
                $s_subtotal = $subtotal[$index];
                $s_discountedPrice = $discountedPrice[$index];
                $s_total = $total[$index];
                $_amountPay = $amountPay[$index];
                $s_returnChange = $returnChange[$index];
                $s_productCode = $productCode[$index];
                $s_category = $category[$index];
                if($selectedCustomer == "PWD" || $selectedCustomer == "Senior Citizen"){
                    $insertCart = $connect->prepare("INSERT INTO tblposorders(id,id_number,product_code,products,quantity,price,variation,category,ordered_date) 
                    VALUES(?,?,?,?,?,?,?,?,?)");
                    $insertCart->bind_param('isssiisss', $ids,$noIdNumber,$s_productCode,$s_productName,$s_quantity,$s_price,$s_variation,$s_category,$orderedDate);
                    $insertCart->execute();
                    if ($insertCart) {
                        header('Location:pos.php?success');
                        unset($_SESSION["cart"]);
                    } else{
                        header('Location:pos.php?error');
                        unset($_SESSION["cart"]);
                    }
                }
                else{
                    $insertCart = $connect->prepare("INSERT INTO tblposorders(id,id_number,product_code,products,quantity,price,variation,category,ordered_date) 
                    VALUES(?,?,?,?,?,?,?,?,?)");
                    $insertCart->bind_param('isssiisss', $ids,$noIdNumber,$s_productCode,$s_productName,$s_quantity,$s_price,$s_variation,$s_category,$orderedDate);
                    $insertCart->execute();
                    if ($insertCart) {
                        header('Location:pos.php?success');
                        unset($_SESSION["cart"]);
                    } else{
                        header('Location:pos.php?error');
                        unset($_SESSION["cart"]);
                    }
                }
            }
            
               if($selectedCustomer == "PWD" || $selectedCustomer == "Senior Citizen"){
                    $insertPOS = $connect->prepare("INSERT INTO tblpos(id,id_number,pwd_senior_number,customer_type,ordered_date,fname,lname,total,discounted_price,amount_pay,amount_change,status) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
                    echo $connect->error;
                    $insertPOS->bind_param('issssssidiis',$posId,$noIdNumber,$pwdSeniorNumber,$selectedCustomer,$orderedDate,$fname,$lname,$total,$discountedPrice,$amountPay,$returnChange,$status);
                    $insertPOS->execute();
                    if ($insertPOS) {
                        header('Location:pos.php?success');
                        unset($_SESSION["cart"]);
                    }
                } else{
                    $insertPOS = $connect->prepare("INSERT INTO tblpos(id,id_number,pwd_senior_number,customer_type,ordered_date,fname,lname,total,discounted_price,amount_pay,amount_change,status) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
                    $insertPOS->bind_param('issssssidiis',$posId,$noIdNumber,$notPwdSenior,$noSelectedCustomer,$orderedDate,$fname,$lname,$total,$noDiscount,$amountPay,$returnChange,$status);
                    $insertPOS->execute();
                    if ($insertPOS) {
                        header('Location:pos.php?success');
                        unset($_SESSION["cart"]);
                    } else{
                        header('Location:pos.php?error');
                        unset($_SESSION["cart"]);
                    }
               }
        }
    }
}
function updateOrderStatus(){
    require 'public/connection.php';
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST['btn-update'])){
            $status = mysqli_real_escape_string($connect,$_POST['orderStatus']);
            $idNumber = mysqli_real_escape_string($connect,$_POST['idNumber']);
            $updateStatus = $connect->prepare("UPDATE tblpos SET status = ? WHERE id_number = ?");
            $updateStatus->bind_param('ss',$status,$idNumber);
            if($updateStatus->execute()){
                header('Location:pos-orders.php');
                $id = null;
                $fullname = $_SESSION['staff-fname']." ".$_SESSION['staff-lname'];
                $sales = $_POST['sales'];
                $userType = "Staff";
                $reportDate = date('Y-m-d h:i:s');
                //insert report sale
                $insertSale = $connect->prepare("INSERT INTO tblreport(id,fullname,sales,user_type,report_date) VALUES(?,?,?,?,?)");
                echo $connect->error;
                $insertSale->bind_param('isiss',$id,$fullname,$sales,$userType,$reportDate);
                $insertSale->execute();
            }
           
        }
    }
}


    
   

addToCart();
insertCart();
updateOrderStatus();
?>