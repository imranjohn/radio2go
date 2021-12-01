<!DOCTYPE html>
<html>
<head>
    <title>How to Generate QR Code in Laravel 8? - ItSolutionStuff.com</title>
</head>
<body>
    
<div class="visible-print text-center" style="text-align: center;">
<a download="image.png" id="downloadOnClick"
 href="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge($path, 0.2, true)->errorCorrection('H')->size(800)->generate($deep_link)) !!} ">

 <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->merge($path, 0.2, true)->errorCorrection('H')->size(800)->generate($deep_link)) !!} ">
     
    
</div>
<script>
  document.getElementById('downloadOnClick').click();
    </script>
    
</body>
</html>