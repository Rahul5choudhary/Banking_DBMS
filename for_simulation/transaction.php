<?php
    use PHPMailer\PHPMailer\PHPMailer;

    session_start();
    $_SESSION['action']='';
    
    if(array_key_exists("login",$_SESSION) and $_SESSION["login"])
    {
        $now=time();
        if($now>$_SESSION['expire'])
        {
          session_destroy();
          // header("location: login.php");
          header('Location: login.php?msg=' . urlencode(base64_encode("You have been successfully logged out!")));
        }
    }
    else
    {
        header("Location:login.php?p=1");
    }
    $link=mysqli_connect("remotemysql.com","IyUUdMcJn4","XU1HaiAhXC","IyUUdMcJn4");
    if(mysqli_connect_error())
    {
        die ('database connection error');
    }
    $string='';
    $to_account='';
    $from_account='';
    $amount='';
    $date=date("Y-m-d");
    $sender='';
    $receiver='';
    if(array_key_exists("submit",$_POST))
    {
        $to_account=$_POST['accountNumber'];
        $amount=$_POST['amount'];
        $from_account=$_SESSION['login'];
        if($to_account==$_SESSION['login']){
            $string='<div class="alert alert-danger" role="alert">
                        Enter where to in account number!!</div>';
        }
        else{
            $query="SELECT count(*) from personal_info where ".$to_account."=account_number";
            if($result=mysqli_query($link,$query))
            {
                $row=mysqli_fetch_array($result);
                if($row[0]==0)
                {
                    $string='<div class="alert alert-danger" role="alert">
                        Enter a valid account number!!</div>';
                }
                else
                {
                     $query="SELECT current_balance FROM balance WHERE ".$from_account."=account_number";
                     if($result=mysqli_query($link,$query))
                        {
                            $row=mysqli_fetch_array($result);
                            if($row[0]<$amount)
                            {
                                $string='<div class="alert alert-danger" role="alert">
                        Not enough balance</div>';
                            }
                            else
                            {   
                                $_SESSION['to_account']=$to_account;
                                $_SESSION['amount']=$amount;
                                $_SESSION['action']=1;
                                $otp=mt_rand(100000,999999);

                                require_once "PHPMailer/PHPMailer.php";
                                require_once "PHPMailer/SMTP.php";
                                require_once "PHPMailer/Exception.php";
                                $mail= new PHPMailer();
                                $mail->isSMTP();
                                $mail->Host='smtp.gmail.com';
                                $mail->SMTPAuth=true;
                                $mail->Username='apnabankcc@gmail.com';
                                $mail->Password='apnabankphp@2';
                                $mail->Port='465';
                                $mail->SMTPSecure='ssl';
                             
                                $mail->isHTML(true);
                                $mail->setFrom('no-reply@apnabank.com');
                                $mail->Subject='Transaction OTP';
                                $mail->Body='your one time password is '.$otp;
                                $mail->addAddress($_SESSION['email']);
                                $to_db=md5(md5($_SESSION['email']).$otp);
                                $query="UPDATE personal_info SET otp='$to_db' WHERE ".$from_account."=account_number";
                                $result=mysqli_query($link,$query);
                                $mail->send();
                                header("location:verify.php?reason=1");
                                // $query="INSERT INTO txn_within_bank(from_account, to_account,date_of_txn,amount) values('$from_account','$to_account','$date','$amount')";
                                // if(mysqli_query($link,$query))
                                // {

                                     // $query="SELECT current_balance FROM balance WHERE ".$from_account."=account_number";
          //                            $result=mysqli_query($link,$query);
          //                            $row=mysqli_fetch_array($result);
          //                            $sender=$row[0];
          //                            $query="SELECT current_balance FROM balance WHERE ".$to_account."=account_number";
          //                            $result=mysqli_query($link,$query);
          //                            $row=mysqli_fetch_array($result);
          //                            $receiver=$row[0];
          //                            $sender=$sender-$amount;
          //                            $receiver=$receiver+$amount;
          //                            $query="UPDATE balance set current_balance='$sender' WHERE ".$from_account."=account_number";
          //                            mysqli_query($link,$query);
          //                            $query="UPDATE balance set current_balance='$receiver' WHERE ".$to_account."=account_number";
          //                            mysqli_query($link,$query);
          //                            $string='<div class="alert alert-success" role="alert">
          //               Transaction successfull</div>';
                                     
                                //} 

                            }
                        }
                        


                }
            }
        }
    }





?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
        integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">


    <!--Google Font-->
    <link href="https://fonts.googleapis.com/css2?family=Mulish&display=swap" rel="stylesheet">

    <!--External CSS-->
    <link rel="stylesheet" href="transaction.css">
    <title>Transaction</title>
    <link rel="icon" type="image/png" href="navicon.svg">


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs=" crossorigin="anonymous"></script>
    <!--External JS-->
    <script src="transaction.js"></script>
</head>

<body>
    <!--
<div class="wrapper">
  <div class="container">
    <div class="title">Transaction Page</div>
    <form class="needs-validation" novalidate>
    <div class="input-form">
      <div class="section-1">
        <div class="items">
          <label class="label">Account Number</label>
          <input type="text" class="input" data-mask="0000 0000 0000 0000" placeholder="XXXX XXXX XXXX XXXX">
        </div>
      </div>
      
      <div class="section-2">
        <div class="items">
          <label class="label">Date of Transaction</label>
          <input type="date" class="input" placeholder="DD / MM / YYYY">
        </div>
        
      </div>
      <div class="section-3">
        <div class="items">
            <label class="label">Amount</label>
          <input type="text" class="input" placeholder="">
        </div>
      </div>
    </div>
   
  <input type="checkbox" required name="terms" id="tick" value="tick" required>
    <label for="tick"> Are you willing to confirm</label>
    <br><br>  
    <div class="btn" href="#">SUBMIT</div>

    <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="invalidCheck" required>
                    <label class="form-check-label" for="invalidCheck">
                        Agree to terms and conditions
                    </label>
                    <div class="invalid-feedback">
                        You must agree before submitting.
                    </div>
                </div>
    <button type="button" class="btn btn-dark btn-lg btn-block">SUBMIT</button>
    </form>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.js"></script>



    THIS SCRIPT IS FOR CHECKING IF THE INFORMATION IS FILLED IN FORM
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>-->

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary p-3 ">
            <!--<div class="container-fluid">-->
                <a class="navbar-brand" href="index.php" id="nm">
                    <img src="navicon.svg" width="30" height="30" class="d-inline-block align-top" alt="" loading="lazy">
                    Apna Bank
                </a>
                <button class="navbar-toggler " type="button" data-toggle="collapse" data-target="#navbarResponsive"
                    aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown bg-primary">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-toggle="dropdown" aria-expanded="false">
                                Services
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="transaction.php">Transfer Money to own bank</a>
                                <a class="dropdown-item" href="tootherbank.php">Transfer Money to other bank</a>
                                <a class="dropdown-item" href="balance.php">current balance</a>
                                <a class="dropdown-item" href="feedback.php">Raise a Complaint</a>
                            </div>
                        </li>
                       
                    <li class="nav-item dropdown bg-primary">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-toggle="dropdown" aria-expanded="false">
                                Account
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="profile.php">Profile</a>
                                
                                <a class="dropdown-item" href="transactionsummary.php">Transction details</a>
                                <a class="dropdown-item" href="deleteAccount.php">Delete Account</a>
                                
                            </div>
                        </li>
                         <li class="nav-item">
                            <a class="nav-link" href="index.php">Home 
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="aboutus.php">About</a>
                        </li>
                       
                        
                        
                    </ul>
                </div>
            <!--</div>-->
            </div>
        </nav>
<!--  end of navbar -->
<!-- alerts -->
    <div>
        <?php echo $string ?>
    </div>
    <div class="wrapper">
        <div class="container">
            <form name="form1" class="needs-validation" novalidate onsubmit="requiredacn()" method="post">
                <div class="title">Transfer Money</div>
                <div class="input-form">
                    <div class="section-1">
                        <div class="items">
                            <label for="account_number">Account Number</label>
                            <input type="text" class="acn" name="accountNumber" id="input" data-mask="0000 0000 0000 0000" placeholder="XXXX XXXX XXXX XXXX" required>
                        </div>
                    </div>

                    <div class="section-3">
                        <div class="items">
                            <label for="amount">Amount</label>
                            <input  name="amount" type="number" min="1" max="10000" class="form-control" id="input" placeholder="" required>
                            <div class="invalid-feedback">
                                Amount should be greater than zero.
                            </div>
                        </div>
                    </div>
                </div>

                <input type="checkbox" required name="terms" value="tick" id="invalidCheck" required>
                <label class="form-check-label" for="invalidCheck">
                    Are you willing to confirm
                </label>
                <div class="invalid-feedback">
                    You must confirm to proceed
                </div>
                <br><br>

                <button type="submit" class="btn btn-dark btn-lg btn-block" name="submit">SUBMIT</button>
                </form>
        </div>
    </div>

     <!--THIS SCRIPT IS FOR CHECKING IF THE INFORMATION IS FILLED IN FORM-->
     <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
    <!--TILL HERE-->
    



    <!-- jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
        crossorigin="anonymous"></script>
</body>

</html>