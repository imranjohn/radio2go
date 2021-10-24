<!DOCTYPE html>
<html>
<head>
    <title>How to Generate QR Code in Laravel 8? - ItSolutionStuff.com</title>
</head>
<body>
    
<div class="visible-print text-center" style="text-align: center;">
     
    <!-- {!! QrCode::format('png')->merge('http://www.radio2go.fm/wp-content/uploads/2021/06/Logo_Radio2Go_weisseSubline.png', 0.0, true)->eye('circle')->size(800)->generate('ItSolutionStuff.com'); !!} -->
        <img src="data:image/png;base64, {!! $image !!} " />
    
</div>
    
</body>
</html>