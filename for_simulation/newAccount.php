<?php
    use PHPMailer\PHPMailer\PHPMailer;
    session_start();
    $_SESSION['action']='';
    
    $string='';
    if(array_key_exists("login",$_SESSION) and $_SESSION["login"])
    {
        header("location: index.php");
    }
    
    
/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
    $link=mysqli_connect("remotemysql.com","IyUUdMcJn4","XU1HaiAhXC","IyUUdMcJn4");
    if(mysqli_connect_error())
    {
        die ('database connection error');
    }
    $f_name='';
    $l_name='';
    $full_name='';     //concatenating into single string
    $email='';
    //$name=$_GET['gender'];
    $phone_number='';
    $address='';
    $city='';
    $state='';
    $zip='';
    $full_address='';     //concatenating into single string
    $password='';
    $txn_password='';
    $dob='';
    $currentDate=date('Y-m-d');
    $string='';
    $otp='';
    if(array_key_exists("submit",$_POST))
    {
        $f_name=$_POST['fname'];
        $l_name=$_POST['lname'];
        $full_name=$f_name." ".$l_name;     //concatenating into single string
        $email=$_POST['email'];
        //$name=$_GET['gender'];
        $phone_number=$_POST['phone'];
        $address=$_POST['address'];
        $city=$_POST['city'];
        $state=$_POST['state'];
        $zip=$_POST['zip'];
        $full_address=$address." ".$city." ".$state." ".$zip;     //concatenating into single string
        $password=md5(md5($email).$_POST['password']);
        $dob=$_POST['dob'];
        $email_txn=bin2hex(random_bytes(4));
        $txn_password=md5(md5($email).$email_txn);
        $d1 =new DateTime($currentDate);
        $d2  = new DateTime($_POST['dob']);

        $diff = $d2->diff($d1);
        if(strlen($phone_number)==10)
        {
            // Attempt insert query execution
            if($diff->y<18 )
            {   
                    $string='<div class="alert alert-danger" role="alert">
                                Age must be 18 or above</div>';
            }
            else
            {    
                $query="SELECT count(email) from personal_info where '".$email."'=email";
                $result=mysqli_query($link,$query);
                $row=mysqli_fetch_array($result);
                if($row[0]==0)
                {
                    $sql = "INSERT INTO personal_info(full_name,email,contact_number,dob,address,password,txn_password,otp)
                                values ('$full_name','$email','$phone_number','$dob','$full_address','$password','$txn_password','$otp')";
                    if(mysqli_query($link, $sql)){
                        
                        $last_id = mysqli_insert_id($link);     // Obtain last inserted id
                        //echo "Records inserted successfully. Last inserted ID is: " . $last_id;
                        // this section will managed automatic generated mail to the new user
                        // and send them a one time trasaction password.
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
                            $mail->Subject='Welcome '.$f_name;
                            $mail->Body='We welcome you to Apna Bank.<br>
                                            Thanks for choosing us.<br>
                                            Your transaction password is '.$email_txn.'
                                            .<br>You can change this by login into your account and modifying
                                            transaction password.';
                            $mail->addAddress($email);
                            $mail->send();

                        // email section ends here
                        header("location: login.php");
                    }
                }
                else
                {
                     $string='<div class="alert alert-danger" role="alert">
                            This email id has already been used.Try another!!</div>';
                }
            }
        }
        else
        {
                $string='<div class="alert alert-danger" role="alert">
                        wrong mobile number</div>';
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
    <link rel="stylesheet" href="newAccountStyle.css">
    <title>Create Account</title>
    <link rel="icon" type="image/png" href="navicon.svg">


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs=" crossorigin="anonymous"></script>
    <!--External JS-->
    <script src="newAccount.js"></script>
    <script src="cities.js"></script>
</head>

<body>


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
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home 
                            </a>
                        </li>
                        <li class="nav-item dropdown bg-primary">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                data-toggle="dropdown" aria-expanded="false">
                                Services
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="transaction.php">Send Money to own bank</a>
                                <a class="dropdown-item" href="tootherbank.php">Send Money to other bank</a>
                                <a class="dropdown-item" href="balance.php">current balance</a>
                                <a class="dropdown-item" href="#">Raise a Complaint</a>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="aboutus.php">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">My Account</a>
                        </li>
                        
                        
                    </ul>
                </div>
            <!--</div>-->
            </div>
        </nav>


    <!--For a alert to check filled info-->
    <div class="alert alert-primary" role="alert">
        Go through the information you filled correctly before submitting.
    </div>
    <div>
        <?php echo $string ?>
    </div>


    <div class="container">
        <div class="info" id="personalInf">
            <p id="pi">Personal Information</p>
        </div>
    </div>
    <!--Bootstrap form-->
    <div class="container">
        <form method="post" class="needs-validation" novalidate>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="fname">First name</label>
                    <input type="text" class="form-control" id="fname" name="fname" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="lname">Last name</label>
                    <input type="text" class="form-control" id="lname" name="lname" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="gender">Gender</label>
                    <select class="form-control" id="gender" name="gender" required>
                        <option selected hidden value=""></option>
                        <option>Male</option>
                        <option>Female</option>
                        <option>Other</option>
                    </select>
                </div>
            </div>


            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com"
                        required>
                    <div class="valid-feedback">
                        Looks good!
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="phone">Contact number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required>
                    <div class="valid-feedback">
                        Looks good!
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="dob">Date of Birth</label>
                    <input type="date" class="form-control" name="dob" id="dob" placeholder="dd-mm-yyyy" required>
                    <div class="valid-feedback">
                        Looks good!
                    </div>
                </div>
            </div>


            <div class="info" id="addressInf">
                <p id="pi">Address Information</p>
            </div>


            <div class="form-row">
                <div class="col-md-12 mb-3">
                    <label for="address">Address</label>
                    <input type="text" name="address" class="form-control" id="address" required>
                    <div class="valid-feedback">
                        Looks good!
                    </div>
                </div>
            </div>


            <div class="form-row">
            <div class="col-md-6 mb-3">
                    <label for="state">State</label>
                    <select onchange="print_city('state', this.selectedIndex);" id="sts" name ="state" class="form-control" required></select>
                   <!-- <input type="text" class="form-control" name="state" id="state" required>
                    <div class="invalid-feedback">
                        Please select a valid state.
                    </div> -->
                </div>

                <div class="col-md-3 mb-3">
                    <label for="city">City</label>
                    <select id ="state" class="form-control" name="city" required></select>
                    <!--<input type="text" class="form-control" name="city" id="city" required>
                    <div class="invalid-feedback">
                        Please provide a valid city.
                    </div>-->
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="zip">Zip</label>
                    <input type="number" class="form-control" name="zip" id="zip" required>
                    <div class="invalid-feedback">
                        Please provide a valid zip.
                    </div>
                </div>
            </div>

            <div class="info" id="accountInf">
                <p id="pi">Account Information</p>
            </div>

            <div class="form-group">
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="accountType">Account type</label>
                        <select class="form-control" name="accountType" id="accountType" required>
                            <option selected hidden value=""></option>
                            <option>Savings</option>
                            <option>Current</option>
                        </select>
                    </div>
                </div>

                <div class="info" id="pswdInf">
                    <p id="pi">Password field</p>
                </div>

                <div class="form-group">
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" placeholder="Enter Password" name="password"
                                id="password" minlength="7"  required>
                                <div class="invalid-feedback">
                                        Please provide password with atleast 7 characters
                                </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="confirmPassword">Confirm Password</label>
                            <input type="password" class="form-control" placeholder="Confirm your Password"
                                name="confirmPassword" id="confirmPassword" minlength="7" required>                                 
                        </div>
                    </div>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="invalidCheck" required>
                    <label class="form-check-label" for="invalidCheck">
                        Agree to terms and conditions
                    </label>
                    <div class="invalid-feedback">
                        You must agree before submitting.
                    </div>
                </div>
            </div>
    </div>
    </div>
    <center>
        <button name="submit" class="btn btn-outline-primary" type="submit">Submit form</button>
    </center>
    </form>
    </div>
    <!-- Footer -->
    <footer class="page-footer font-small blue  pt-4">

        <div class="footer-copyright text-center py-3">© 2020 Copyright: Apna Bank
        </div>

    </footer>
    <!-- Footer -->

    <script language="javascript">print_state("sts");</script>
    <script>$('.selectpicker').selectpicker({
        dropupAuto: false
    });</script>
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