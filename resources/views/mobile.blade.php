<!DOCTYPE html>
<html>
<head>
    <title>How to Generate QR Code in Laravel 8? - ItSolutionStuff.com</title>
</head>
<body>
    
<div class="visible-print text-center" style="text-align: center;">
<a download="image.png" id="downloadOnClick" href="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('logo_round.png', 0.2, true)->errorCorrection('H')->size(800)->generate($deep_link)) !!} "><img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge('logo_round.png', 0.2, true)->errorCorrection('H')->size(800)->generate($deep_link)) !!} ">
     
    <!-- {!! QrCode::format('png')->merge('http://www.radio2go.fm/wp-content/uploads/2021/06/Logo_Radio2Go_weisseSubline.png', 0.0, true)->eye('circle')->size(800)->generate('ItSolutionStuff.com'); !!} -->
        <!-- <img src="data:image/png;base64, {!! $image !!} " /> -->
    
</div>
<script>
  document.getElementById('downloadOnClick').click();
    </script>
    
</body>
</html>