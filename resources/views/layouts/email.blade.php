<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- <style> -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
  
  <table class="body">
    <tr>
      <td class="center" align="center" valign="top">
        <center>

            @yield('body')

        </center>
      </td>
    </tr>
  </table>
  <!-- prevent Gmail on iOS font size manipulation -->
  <div style="display:none; white-space:nowrap; font:15px courier; line-height:0;"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </div>
</body>
</html>