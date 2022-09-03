<?php
use PHPMailer\PHPMailer\PHPMailer;
function updateBookStatus(){
    require 'public/connection.php';
    if(isset($_SERVER["REQUEST_METHOD"]) == "POST"){
        if(isset($_POST['btn-update'])){
            date_default_timezone_set("Asia/Manila");
            $customerName = $_POST['customerName'];
            $refNumber = $_POST['refNumber'];
            $id = $_POST['id'];
            $email = $_POST['email'];
            $createAt = $_POST['reservedDate'];
            $bookStatus = $_POST['bookStatus'];
            $guests = $_POST['guests'];
            $schedDate = date('M-d-Y',strtotime($_POST['schedDate']));
            $schedTime = $_POST['schedTime'];
            $notifDate = date('Y-m-d h:i:s');
            $firstLetter = substr($customerName,0,1);
            $logo = "https://i.ibb.co/CMq6CXs/logo.png";
            require 'php-mailer/vendor/autoload.php';
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '';
            $mail->Password = '';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->setFrom('mangmacsmarinerospizzahouse@gmail.com', "Mang Mac's Marinero");
            $mail->addAddress($email);
            $mail->isHTML(true);
            if($bookStatus == "Reserved"){
                $updateOrderStatus = $connect->prepare("UPDATE tblorderdetails SET order_status=? WHERE order_number=?");
                $updateOrderStatus->bind_param('ss',$bookStatus,$refNumber);
                $updateOrderStatus->execute();
                $updateBookStatus = $connect->prepare("UPDATE tblreservation SET status=? WHERE id=?");
                $updateBookStatus->bind_param('si',$bookStatus,$id);
                $updateBookStatus->execute();
                if($updateBookStatus){
                    function pushNotifcation($sendTo,$data){
                        $apiKey = "AAAAozYNVDs:APA91bFDRuJDQZCnFaAmQFP_uTUUzp9fYQZRJI01XtZ34XYr1ifB2f7jDa1R7WVxavsv-hSZZ7qivrEUk37O7-s1VcB8wMJuhIW0R6-ldwv9UQnxlJssMGvEdOq7admem2vfrCkAUqo2";
                        $url = "https://fcm.googleapis.com/fcm/send";
                        $fields = json_encode(array('to'=>$sendTo,'notification'=>$data));
                        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL,$url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS,($fields));
                    
                        $headers = array();
                        $headers[] = 'Authorization:key='.$apiKey;
                        $headers[] = 'Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    
                        $result = curl_exec($ch);
                        if (curl_errno($ch)) {
                            echo 'Error:' . curl_error($ch);
                        }
                        curl_close($ch);
                        }
                        $sendTo = $_POST['token'];
                        $data = array(
                            'title'=>"$bookStatus",
                            'body'=>"Hello $customerName,\nyour table reservation for $guests guests  is already confirmed. See you at $schedDate - $schedTime"
                        );
                        pushNotifcation($sendTo,$data);
                        $mail->Subject = "Your Booking #".$refNumber." has been confirmed";
                        $mail->Body = "
                        <div class='container' style='padding: 1rem;'>
                        <div style='display: flex; flex-direction: column; align-items: center;'>
                            <div class='logo-section'>
                                <img src='$logo' width='50'>
                                <strong>Mang Macs's Food Shop</strong>
                            </div>
                            <div class='icon-section' style='padding: 1rem;'>
                                <i class='fa-regular fa-circle-check'></i>
                            </div>
                            <div style='text-align: center;'>
                                <p>Hello ".$customerName."</p>
                                <h3>Your table reservation for ".$guests." guests is already confirmed. See you at </h3> 
                            </div>
                        </div>
                        <hr style='border:0.3px solid #dbdbdb;'>
                        <div>
                            <strong>Booking Summary</strong>
                        </div>
                        <div style = 'style:display:flex; flex-direction:column;'>
                            <div style='display: flex; justify-content: space-between;'>
                                <p style='width:150px;'>Booking Number:</p>
                                <p>".$id."</p>
                            </div>
                            <div style='display: flex; justify-content: space-between; margin: -20px 0;'>
                                <p style='width:150px;'>Guests:</p>
                                <p>".$guests."</p>
                            </div>
                            <div style='display: flex; justify-content: space-between; margin: -20px 0;'>
                                <p style='width:150px;'>Schedule:</p>
                                <p>".$createAt."</p>
                            </div>
                            <div style='display: flex; justify-content: space-between;'>
                                <p style='width:150px;'>Placed on:</p>
                                <p>".$schedDate + $schedTime."</p>
                            </div>
                        </div>
                        <hr style='border:0.3px solid #dbdbdb;'>
                        <div style='text-align:center';>
                            <p>from</p></br>
                            <h3>Mang Mac' s Food Shop</h3></br>
                            <p>Zone 5, Barangay Sta. Lucia Bypass Road, Urdaneta, Philippines</p>
                        </div>
                    </div>
                    ";
                    $mail->AltBody = 'FROM: mangmacsmarinerospizzahouse@gmail.com';
                    $mail->send();
                    $mail->Subject("Table Reservation Slip");
                    $mail->Body ="  <div style='background:#ffffff; border:15px solid #36E49A; font-family:arial;  width:450px;   margin:0 auto;  padding:20px;'>
                        <div style='padding:5px; text-align:center;'>
                        <div style='background:#000;  color:#fff;   font-size:2rem; width:70px; height:70px; border-radius:50%; margin:auto;'>
                                <p><span style='line-height:70px;'>$firstLetter</span></p>
                            </div>
                            <div>
                                <p>$customerName</p>
                            </div>
                        </div>
                        <div style='border:1px solid #000;  margin:auto; text-align:center;'>
                            <p>Order #: $refNumer</p>
                        </div>
                        <p style='color:#6F6F6F; font-size:12px; text-align:center;'><?php date_default_timezone_set('Asia/Manila'); echo date('Y-m-d h:i a')?></p>
                        <hr>
                        <div style='color:#6F6F6F; margin:20px 0;'>
                            <div>
                                <span>Email:</span>
                                <span style='float:right;'>$email</span>
                            </div>
                            <div style='margin:20px 0;'>
                                <span>Name:</span>
                                <span style='float:right;'>$customerName</span>
                            </div>
                            <div>
                                <span>Guests:</span>
                                <span style='float:right;'>$guests</span>
                            </div>
                            <div>
                                <span>Scheduled Date:</span>
                                <span style='float:right;'>$schedDate $schedTime</span>
                            </div>
                        </div>
                        <hr>
                        <div>
                            <p style='color:#6F6F6F; font-size:12px; text-align:center;'>Please show this to the counter to process your table reservation.</p>
                        </div>
                        <div>
                            <h1 style='color:#36E49A; font-size:1.3rem; text-align:center;'>MANG MAC'S MARINEROS  <br> PIZZA HOUSE</h1>
                        </div>
                    </div>";
                    $mail->AltBody = 'FROM: mangmacsmarinerospizzahouse@gmail.com';
                    $mail->send();
                    header('Location:reservation.php?updated');
                }
            }
            else{
                $updateBookStatus = $connect->prepare("UPDATE tblreservation SET status=? WHERE id=?");
                $updateBookStatus->bind_param('si',$bookStatus,$id);
                $updateBookStatus->execute();
                if($updateBookStatus){
                    function pushNotifcation($sendTo,$data){
                        $apiKey = "AAAAozYNVDs:APA91bFDRuJDQZCnFaAmQFP_uTUUzp9fYQZRJI01XtZ34XYr1ifB2f7jDa1R7WVxavsv-hSZZ7qivrEUk37O7-s1VcB8wMJuhIW0R6-ldwv9UQnxlJssMGvEdOq7admem2vfrCkAUqo2";
                        $url = "https://fcm.googleapis.com/fcm/send";
                        $fields = json_encode(array('to'=>$sendTo,'notification'=>$data));
                        // Generated by curl-to-PHP: http://incarnate.github.io/curl-to-php/
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL,$url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS,($fields));
                    
                        $headers = array();
                        $headers[] = 'Authorization:key='.$apiKey;
                        $headers[] = 'Content-Type: application/json';
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    
                        $result = curl_exec($ch);
                        if (curl_errno($ch)) {
                            echo 'Error:' . curl_error($ch);
                        }
                        curl_close($ch);
                        }
                        $sendTo = $_POST['token'];
                        $data = array(
                            'title'=>"$bookStatus",
                            'body'=>"Hello $customerName,\nsorry, there is no available table for accomodation."
                        );
                        pushNotifcation($sendTo,$data);
                    $mail->Subject = "Your reservation #".$refNumber." is not available";
                        $mail->Body = "
                        <div class='container' style='padding: 1rem;'>
                        <div style='display: flex; flex-direction: column; align-items: center;'>
                            <div class='logo-section'>
                                <img src='$logo' width='50'>
                                <strong>Mang Macs's Food Shop</strong>
                            </div>
                            <div class='icon-section' style='padding: 1rem;'>
                                <i class='fa-regular fa-circle-check'></i>
                            </div>
                            <div style='text-align: center;'>
                                <p>Hello ".$customerName."</p>
                                <h3>Sorry, there is no available table for accomodation.</h3> 
                                <p>See you next time.&#128512;</p>
                            </div>
                        </div>
                        <hr style='border:0.3px solid #dbdbdb;'>
                        <div>
                            <strong>Booking Summary</strong>
                        </div>
                        <div style = 'style:display:flex; flex-direction:column;'>
                            <div style='display: flex; justify-content: space-between;'>
                                <p style='width:150px;'>Booking Number:</p>
                                <p>".$id."</p>
                            </div>
                            <div style='display: flex; justify-content: space-between; margin: -20px 0;'>
                                <p style='width:150px;'>Guests:</p>
                                <p>".$guests."</p>
                            </div>
                            <div style='display: flex; justify-content: space-between; margin: -20px 0;'>
                                <p style='width:150px;'>Schedule:</p>
                                <p>".$createAt."</p>
                            </div>
                            <div style='display: flex; justify-content: space-between;'>
                                <p style='width:150px;'>Placed on:</p>
                                <p>".$schedDate + $schedTime."</p>
                            </div>
                        </div>
                        <hr style='border:0.3px solid #dbdbdb;'>
                        <div style='text-align:center';>
                            <p>from</p></br>
                            <h3>Mang Mac' s Food Shop</h3></br>
                            <p>Zone 5, Barangay Sta. Lucia Bypass Road, Urdaneta, Philippines</p>
                        </div>
                    </div>
                    ";
                    $mail->AltBody = '';
                    $mail->send();
                    header('Location:reservation.php?updated');
                }
            }
        }
    }
}

updateBookStatus();
?>
