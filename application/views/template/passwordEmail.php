<!DOCTYPE html>
<html>
<head>
    <title>Email</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
   <!--  <link rel="stylesheet" href="http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script> -->
</head>
<body style="text-align: center; font-family:'Lato',Arial,sans-serif; overflow: hidden; overflow-y: scroll;">
    <div class="cover" style="width: 100%; margin: 0 auto; background-color: #f5f5f5;">
        <div class="container one" style="width: 100%; height: 50px; background-color: #0A434E; border-radius: 7px 7px 0 0;">
            <div class="pull-left" style="padding: 10px; float: left;">
                <a href="<?php echo prep_url('www.dawati.co.ke') ?>" target="_blank"><img src="<?php echo prep_url('www.dawati.co.ke/assets/images/logo.jpg') ?>" alt="dawati"></a>
            </div>
            <div class="pull-right" style="padding: 10px; float: right;">
                <p style="color: #ffffff;">Redefining education</p>
            </div> 
        </div>
        <div class="container two" style="width: 100%; border-radius: 0 0 7px 7px;">
            <div class="row" style="width: 70%; margin: 0 auto;">
                <p style="line-height:24px;font-size:18px">We received a request to reset your password. You can do this by clicking the link below.</p>
                <a href="<?php echo base_url('resetHelper/'.$hash) ?>" target="_blank">Reset password</a>
                <p>If the link is not clickable, copy the following url to your browser: <?php echo base_url('resetHelper/'.$hash) ?> </p>
                <p style="color:#999;font-size:15px;font-weight:700;margin-top:20px;padding:20px 0;font-size:12px;line-height:1.2em;border-top:1px solid #edeff0">If you are not trying to reset your Dawati login credentials, please ignore this email. Your password won't change until you access the link above and create a new one. For any queries, email us at <a href="mailto:support@dawati.co.ke">support@dawati.co.ke</a></p>
            </div>

        <div class="page-footer-inner"> <?php echo date('Y')?> &copy; Dawati | All Rights Reserved <br><small> CARREL Technologies Limited, Nairobi, Kenya </small>
        </div>
    </div>
</body>
</html>