{__NOLAYOUT__}
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>温馨提示</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://cdn.demo.fastadmin.net/assets/img/favicon.ico" />
    <style type="text/css">
        *{box-sizing:border-box;margin:0;padding:0;font-family:Lantinghei SC,Open Sans,Arial,Hiragino Sans GB,Microsoft YaHei,"微软雅黑",STHeiti,WenQuanYi Micro Hei,SimSun,sans-serif;-webkit-font-smoothing:antialiased}
        body{padding:20px 0;background:#fff;font-weight:400;font-size:1pc;-webkit-text-size-adjust:none;color:#333}
        a{outline:0;color:#3498db;text-decoration:none;cursor:pointer}
        .system-message{margin:0px 5%;padding:0px 20px;background:#fff;box-shadow:1px 0px 1px hsla(0,0%,39%,.1);text-align:center}
        .system-message h1{margin:0;margin-top:5pt;margin-bottom:9pt;color:#444;font-weight:400;font-size:30px}
        .system-message .jump,.system-message .image{margin:20px 0;padding:0;padding:10px 0;font-weight:400}
        .system-message .jump{font-size:14px}
        .system-message .jump a{color:#333}
        .system-message p{font-size:9pt;line-height:20px}
        .system-message .btn{display:inline-block;margin-right:10px;width:138px;height:2pc;border:1px solid #44a0e8;border-radius:30px;color:#44a0e8;text-align:center;font-size:1pc;line-height:2pc;margin-bottom:5px;}
        .success .btn{border-color:#69bf4e;color:#69bf4e}
        .error .btn{border-color:#ff8992;color:#ff8992}
        .info .btn{border-color:#3498db;color:#3498db}
        .copyright p{width:100%;color:#919191;text-align:center;font-size:10px;}
        .system-message .btn-grey{border-color:#bbb;color:#bbb}
        .clearfix:after{clear:both;display:block;visibility:hidden;height:0;content:"."}
        @media (max-width:768px){body {padding:20px 0;}}
            @media (max-width:480px){.system-message h1{font-size:30px;}}
    </style>
</head>
<body>

<div class="system-message error">
    <div class="image">
        <?php switch ($code) {?>
        <?php case 1:?>
        <img src="/static/admin/success.svg" alt="" width="180" />
        <?php break;?>
        <?php case 0:?>
        <img src="/static/admin/error.svg" alt="" width="150" />
        </div>
        <?php break;?>
        <?php } ?>
        <h1><?php echo(strip_tags($msg));?></h1>
        <p class="jump">
            页面将在 <span id="wait"><?php echo($wait);?></span> 秒后自动跳转        </p>
        <p class="clearfix">
            <a href="/" class="btn btn-grey">返回首页</a>
            <a href="<?php echo($url);?>" class="btn btn-primary">立即跳转</a>
        </p>
    </div>
    <div class="copyright" style="display:none">
        <p>Powered by <a href="javascript:;">HuiCmf</a></p>
    </div>
    {if $url}
        <script type="text/javascript">
          (function () {
            var wait = document.getElementById('wait');
            var interval = setInterval(function () {
              var time = --wait.innerHTML;
              if (time <= 0) {
                location.href = history.length <= 1 ? "/" : "{$url}";
                clearInterval(interval);
              }
            }, 1000);
          })();
        </script>
    {/if}
</body>
</html>