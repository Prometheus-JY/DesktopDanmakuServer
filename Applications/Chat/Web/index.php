<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>弹幕系统</title>
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/jquery-sinaEmotion-2.1.0.min.css" rel="stylesheet">
  <link href="/css/style.css" rel="stylesheet">
	
  <script type="text/javascript" src="/js/swfobject.js"></script>
  <script type="text/javascript" src="/js/web_socket.js"></script>
  <script type="text/javascript" src="/js/jquery.min.js"></script>
  <script type="text/javascript" src="/js/jquery-sinaEmotion-2.1.0.min.js"></script>

</head>
<body onload="connect();">
    <div class="container">
	    <div class="row clearfix">
	        <div class="col-md-1 column">
	        </div>
	        <div class="col-md-6 column">
	           <form onsubmit="onSubmit(); return false;">
                    <textarea class="textarea thumbnail" id="textarea"></textarea>
                    <div class="say-btn">
                        <input type="submit" class="btn btn-default" value="发表" />
                    </div>
             </form>
            
	        </div>   
	    </div>
    </div>
    <script type="text/javascript">
      // 动态自适应屏幕
      document.write('<meta name="viewport" content="width=device-width,initial-scale=1">');
      $("textarea").on("keydown", function(e) {
          // 按enter键自动提交
          if(e.keyCode === 13 && !e.ctrlKey) {
              e.preventDefault();
              $('form').submit();
              return false;
          }

          // 按ctrl+enter组合键换行
          if(e.keyCode === 13 && e.ctrlKey) {
              $(this).val(function(i,val){
                  return val + "\n";
              });
          }
      });
    </script>
</body>


<script type="text/javascript">
    if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
    // 如果浏览器不支持websocket，会使用这个flash自动模拟websocket协议，此过程对开发者透明
    WEB_SOCKET_SWF_LOCATION = "/swf/WebSocketMain.swf";
    // 开启flash的websocket debug
    WEB_SOCKET_DEBUG = true;
    var ws, name, client_list={};


    var danmu;

    // 连接服务端
    function connect() {
       // 创建websocket
       ws = new WebSocket("ws://"+document.domain+":6973");
       // 当socket连接打开时，输入用户名
       ws.onopen = onopen;
       // 当有消息时根据消息类型显示不同信息
       ws.onmessage = onmessage; 
       ws.onclose = function() {
        console.log("连接关闭，定时重连");
          connect();
       };
       ws.onerror = function() {
        console.log("出现错误");
       };
    }

    // 连接建立时发送登录信息
    function onopen()
    {
        // 登录
        var login_data = '{"type":"login","token":"123456"}';
        console.log("websocket握手成功，发送登录数据:"+login_data);
        ws.send(login_data);
    }

    // 服务端发来消息时
    function onmessage(e)
    {
        console.log(e.data);
        var data = JSON.parse(e.data);
        switch(data['type']){
            // 服务端ping客户端
            case 'ping':
                ws.send('{"type":"pong"}');
                break;
            case 'error':
              if(data['content']=='teacher not found'){
                alert("老师不在线");
              }

              var input = document.getElementById("textarea");
              input.value = danmu;  
              break;
        }
    }

    // 提交对话
    function onSubmit() {
      var input = document.getElementById("textarea");
      danmu = input.value.replace(/"/g, '\\"').replace(/\n/g,'\\n').replace(/\r/g, '\\r');
      ws.send('{"type":"danmu","content":"'+danmu+'"}');
      input.value = "";
      input.focus();
    }
</script>
</html>
