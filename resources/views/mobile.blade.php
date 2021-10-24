<?php
$PATH_INFO = $_SERVER['PATH_INFO'];//pass from htaccss

$PI = explode("/", mb_substr(trim($PATH_INFO), 1));

//Detect special conditions devices
$iPod = stripos($_SERVER['HTTP_USER_AGENT'], "iPod");
$iPhone = stripos($_SERVER['HTTP_USER_AGENT'], "iPhone");
$iPad = stripos($_SERVER['HTTP_USER_AGENT'], "iPad");
$Android = stripos($_SERVER['HTTP_USER_AGENT'], "Android");
$AndroidDownurl = "https://play.google.com/store/apps/details?id=com.domain.linkingappsetup";
$iOSDownurl = "https://apps.apple.com/us/app/radio-2go/id1588788883";
$deeplink_url = "radio2go://com.letech.radio2go/4ssdf/sdfsdf";

$app_url = $iOSDownurl;

?>
<!DOCTYPE html>
<html>
    <head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# website: http://ogp.me/ns/website#">
        <title><?php echo "sdfsdf"; ?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <link rel="icon" href="../images/Your_icon_ico.ico" type="image/x-icon" />
        <link rel="shortcut icon" href="../images/Your_icon_ico.ico" type="image/x-icon" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
     
        <?php
        if (!empty($og_image)) {
            ?>
          
            <?php
        }
        ?>
    </head>
    <body>
        <div class="row">
            <div class="col-md-12">
                <a class="btn btn-block"><img src="../images/Your_app_icon.png" height="100"/></a>
                <a class="btn btn-block" href="<?php echo $AndroidDownurl;?>"><img src="../images/Google_Play.png" height="130"/></a>
                <a class="btn btn-block" href="<?php echo $iOSDownurl;?>"><img src="../images/Apple_Store.png" height="100"/></a>
            </div>
        </div>
        <script type="text/javascript">
            setTimeout(function () {
                location.href = "<?php echo $iOSDownurl; ?>";
                return false;
            }, 500);
        </script>
    </body>
</html>
