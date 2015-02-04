<?php
#Music page. Version 1, 2015feb03a04.
#from http://stackoverflow.com/questions/845021/how-to-get-useful-error-messages-in-php
ini_set('display_startup_errors',1);
error_reporting(-1);
ini_set('display_errors', 1);
$fragmentA = <<<'EOD'
<!doctype html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css" media="all">
      @font-face{
        font-family:'anoeyfuturamerlincommedium';
        src:url('d/f/anoeyfuturamerlincom2.61.eot');
        src:url('d/f/anoeyfuturamerlincom2.61.eot?#iefix') format('embedded-opentype'),url('d/f/anoeyfuturamerlincom2.61.woff') format('woff'),url('d/f/anoeyfuturamerlincom2.61.ttf') format('truetype'),url('d/f/anoeyfuturamerlincom2.61.svg#anoeyfuturamerlincommedium') format('svg');
        font-weight:normal;
        font-style:normal}
      @font-face{
        font-family:'wreatherweb';
        src:url('d/f/wreathe-r.eot');
        src:url('d/f/wreathe-r.eot?#iefix') format('embedded-opentype'),url('d/f/wreathe-r.woff') format('woff'),url('d/f/wreathe-r.ttf') format('truetype'),url('d/f/wreathe-r.svg#wreatherweb') format('svg');
        font-weight:normal;
        font-style:normal}
      @font-face{
        font-family:'wreatherweb';
        src:url('d/f/wreathe-b.eot');
        src:url('d/f/wreathe-b.eot?#iefix') format('embedded-opentype'),url('d/f/wreathe-b.woff') format('woff'),url('d/f/wreathe-b.ttf') format('truetype'),url('d/f/wreathe-b.svg#wreathebold') format('svg');
        font-weight:bold;
        font-style:normal}
      @font-face{
        font-family:'wreatherweb';
        src:url('d/f/wreathe-i.eot');
        src:url('d/f/wreathe-i.eot?#iefix') format('embedded-opentype'),url('d/f/wreathe-i.woff') format('woff'),url('d/f/wreathe-i.ttf') format('truetype'),url('d/f/wreathe-i.svg#wreatheitalic') format('svg');
        font-weight:normal;
        font-style:italic}
      @font-face{
        font-family:'wreatherweb';
        src:url('d/f/wreathe-bi.eot');
        src:url('d/f/wreathe-bi.eot?#iefix') format('embedded-opentype'),url('d/f/wreathe-bi.woff') format('woff'),url('d/f/wreathe-bi.ttf') format('truetype'),url('d/f/wreathe-bi.svg#wreathebold_italic') format('svg');
        font-weight:bold;
        font-style:italic}
      html{
        background:#d5e3cf;
        background:-webkit-gradient(radial,50% 0,0,50% 0,300,from(#FFF),to(#d5e3cf));
        background:-moz-radial-gradient(50% 0 90deg,circle farthest-side,#FFF,#d5e3cf,#d5e3cf 31.25em);
        background-repeat:no-repeat;
        background-color:#d5e3cf}
      body{
        text-align:justify;
        overflow-x:hidden;
        min-height:18.75em}
      form{
        display:inline}
      pre,blockquote,li,table,tbody,tr,td,ul,ol{
        color:#22520f;
        font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;
        table-layout:fixed}
      a{
        color:#520f22;
        font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;
        text-decoration:none;
        -webkit-transition:all .8s ease-out;
        -moz-transition:all .8s ease-out;
        -o-transition:all .8s ease-out;
        transition:all .8s ease-out}
      a:hover{
        color:#520f22;
        font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;
        text-decoration:underline}
      p{
        color:#22520f;
        font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;
        word-wrap:break-word;
        text-indent:30pt;
        text-align:justify;
        margin-top:0;
        margin-bottom:0}
      table{
        border-color:transparent}
      input{
        background-color:transparent}
      h1{
        color:#22520f;
        font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;
        font-weight:normal;
        text-align:center}
      h2,h3,h4,h5,h6{
        color:#22520f;
        font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;
        font-weight:normal;
        font-style:italic;
        text-align:center}
      .t{
        border:0;
        background-color:transparent;
        padding:0;
        overflow:visible;
        font-size:1em;
        color:#520f22;
        font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif}
      .t:hover{
        border:0;
        background-color:transparent;
        padding:0;
        overflow:visible;
        font-size:1em;
        color:#520f22;
        font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;
        text-decoration:underline;
        cursor:pointer}
      div.floattb{
        position:fixed;
        bottom:0;
        left:0;
        width:100%;
        z-index:3;
        text-align:center}
      div.floatbg{
        left:25px;
        right:25px;
        position:fixed;
        bottom:0;
        height:25px;
        z-index:2;
        opacity:.85;
        background-color:#f0f0f0}
      a.floatlink{
        font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;
        text-decoration:none}
      a.floatlink:hover{
        font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;
        text-decoration:underline}
      div.generated-toc{
        text-align:left;
        list-style-type:none;
        position:fixed;
        bottom:25px;
        left:35px;
        width:25%;
        z-index:2;
        background-color:#efefef;
        font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;
        text-decoration:none}
      div#hideshow{
        position:fixed;
        bottom:0;
        left:-12px;
        width:10%;
        z-index:4;
        text-align:left}
      div#generated-toc a{
        font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;
        text-decoration:none;
        overflow-y:scroll}
      div#generated-toc ul{
        text-indent:-10pt;
        list-style-type:none;
        font-size:x-small}
      a#generated_toc_d_toggle:hover{
        text-decoration:none}
      p#toggle-container{
        text-align:left}
      div.greenpage{
        position:absolute;
        top:0;
        min-height:18.75em;
        background-color:transparent;
        margin:8px;
        margin-right:8px;
        z-index:-1}
      div.fh{
        left:25px;
        right:25px;
        position:absolute;
        top:25px;
        height:25px;
        z-index:100;
        text-align:center;
        font-size:large}
      div.litem{
        padding:10px;
        text-align:center}
      div.smalllink{
        padding-top:20px;
        text-align:center;
        font-size:x-small}
      div.relative{
        position:relative;
        padding-top:0}
      .reveal-modal-bg{
        position:fixed;
        height:100%;
        width:100%;
        background:#000;
        background:rgba(0,0,0,.8);
        z-index:100;
        display:none;
        top:0;
        left:0}
      .reveal-modal{
        visibility:hidden;
        top:75px;
        left:0;
        margin-left:-10px;
        width:90%;
        max-width:900px;
        background:#eee url(g.png) no-repeat -200px -80px;
        position:absolute;
        z-index:101;
        padding:0;
        -moz-border-radius:5px;
        -webkit-border-radius:5px;
        border-radius:5px;
        -moz-box-shadow:0 0 10px rgba(0,0,0,.4);
        -webkit-box-shadow:0 0 10px rgba(0,0,0,.4);
        -box-shadow:0 0 10px rgba(0,0,0,.4)}
      .reveal-modal .close-reveal-modal{
        font-size:22px;
        line-height:.5;
        position:absolute;
        top:8px;
        right:11px;
        color:#aaa;
        font-weight:bold;
        cursor:pointer}
      div.logobox{
        margin:auto;
        display:inline-block;
        position:relative;
        height:20%;
        width:auto;
        padding-top:16px;
        padding-left:24px;
        float:left;
        padding-right:75px}
      div.holder{
        -webkit-box-shadow:0 0 10px 5px #FFF;
        box-shadow:0 0 10px 0 #FFF;
        margin:auto;
        position:relative;
        left:25px;
        width:322px;
        display:inline-block}
      div.caption{
        color:white;
        font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;
        text-decoration:none}
      div.captionbg{
        position:absolute;
        bottom:0;
        left:0;
        width:100%;
        height:100%;
        background:#000;
        background:url("data:image/svg+xml;
          base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSI5MCUiIHN0b3AtY29sb3I9IiMwMDAwMDAiIHN0b3Atb3BhY2l0eT0iMSIvPgogICAgPHN0b3Agb2Zmc2V0PSI5MCUiIHN0b3AtY29sb3I9IiMwMDAwMDAiIHN0b3Atb3BhY2l0eT0iMSIvPgogICAgPHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSIjNjA2MDYwIiBzdG9wLW9wYWNpdHk9IjEiLz4KICAgIDxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzI2MjYyNiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgPC9saW5lYXJHcmFkaWVudD4KICA8cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIiBmaWxsPSJ1cmwoI2dyYWQtdWNnZy1nZW5lcmF0ZWQpIiAvPgo8L3N2Zz4=");
          background:-moz-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);
        background:-webkit-gradient(linear,left top,left bottom,color-stop(90%,rgba(0,0,0,1)),color-stop(90%,rgba(0,0,0,1)),color-stop(100%,rgba(40,40,40,1)),color-stop(100%,rgba(38,38,38,1)));
        background:-webkit-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);
        background:-o-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);
        background:-ms-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);
        background:linear-gradient(to bottom,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#000000',endColorstr='#282828',GradientType=0);
        opacity:.8;
        z-index:-1}
      img.logo{
        border-right:1px solid #666;
        border-left:1px solid #666;
        border-top:1px solid #666;
        z-index:105}
      div.caption:hover{
        color:white;
        font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;
        text-decoration:underline}
      a.captionlink{
        color:white}
      div#paddingbottom{
        height:16px}
      div#content{
        position:static;
        padding-top:55px;
        padding-bottom:35px}
      div#page{
        position:absolute;
        left:50%;
        width:640px;
        margin-left:-320px}
      a:focus,a:active,button,input[type="reset"]::-moz-focus-inner,input[type="button"]::-moz-focus-inner,input[type="submit"]::-moz-focus-inner,select::-moz-focus-inner,input[type="file"]>input[type="button"]::-moz-focus-inner{
        outline:none !important}
      .popuplink{
        color:white !important}
      a#tl{
        font-size:.93em !important}
    </style>
    <style type="text/css" media="all">
      a#tl{
        color:#FFF!important;
        z-index:110!important}
      div.floattb{
        z-index:110!important;
        color:#FFF!important}
      div{
        color:#fff!important;
        font-family:wreatherweb,Wreathe,'Centaur MT Std','Centaur MT',Centaur,serif;
        word-wrap:break-word;
        margin-top:0;
        margin-bottom:0;
        text-align:left}
      table,tbody,tr,td{
        color:#fff;
        font-family:wreatherweb,Wreathe,'Centaur MT Std','Centaur MT',Centaur,serif;
        table-layout:fixed}
      table{
        border:1px solid #FFF!important}
      td{
        border:1px dotted #FFF!important}
      div.floatbg{
        z-index:105!important;
        opacity:0.25!important}
      a{
        color:#aaf!important}
      p.mdate{
        padding-bottom:15px}
      h2{
        color:#FFF!important;
        font-style:normal!important}
      html{
        min-height:100%!important;
        position:relative!important}
      body{
        height:100%!important;
        overflow:hidden!important}
      div#mconstraints{
        position:fixed;
        bottom:0;
        top:0;
        right:0;
        left:0}
      #content{
        position:fixed!important;
        bottom:0!important;
        top:0!important;
        right:0!important;
        left:0!important}
      div#musicwrapper{
        -webkit-box-shadow:0 0 5px 0 rgba(255,255,255,1);
        -moz-box-shadow:0 0 5px 0 rgba(255,255,255,1);
        box-shadow:0 0 5px 0 rgba(255,255,255,1);
        top:-80px;
        max-width:680px!important;
        z-index:100;
        height:100%;
        position:relative;
        margin-left:auto;
        margin-right:auto;
        border:1px #888 solid;
        background-color:#111;
        width:100%}
      div#intro{
        left:0!important}
      div#musicthumbcontainer{
        background-color:#333;
        float:left;
        max-width:30%;
        width:180px;
        z-index:110;
        position:absolute;
        top:36px;
        bottom:0;
        overflow-y:auto;
        overflow-x:hidden}
      div#musicthumbnails{
        position:relative;
        bottom:0;
        height:100%}
      div.musicbig{
        display:none;
        position:absolute;
        bottom:0;
        margin-left:30%;
        top:36px;
        right:0;
        background-color:#111;
        overflow-y:scroll;
        padding:15px}
      img.musicbigimage{
        width:100%;
        -webkit-box-shadow:0 0 15px 0 rgba(255,255,255,1);
        -moz-box-shadow:0 0 15px 0 rgba(255,255,255,1);
        box-shadow:0 0 15px 0 rgba(255,255,255,1)}
      iframe.musicbigplayer{
        width:100%;
        -webkit-box-shadow:0 0 15px 0 rgba(255,255,255,1);
        border:0;
        -moz-box-shadow:0 0 15px 0 rgba(255,255,255,1);
        box-shadow:0 0 15px 0 rgba(255,255,255,1)}
      img.musicthumb{
        width:80%;
        max-width:150px;
        -webkit-box-shadow:0 0 8px 0 rgba(255,255,255,1);
        -moz-box-shadow:0 0 8px 0 rgba(255,255,255,1);
        box-shadow:0 0 8px 0 rgba(255,255,255,1);
        cursor:pointer;
        margin:10%}
      img.musicthumb:hover{
        border-left:3px #FFF solid}
      .mlefti{
        text-align:left!important;
        padding-left:3px}
      .audiojs{
        position:relative;
        top:0;
        right:0;
        width:100%!important}
      div#bandcamplink{
        position:fixed;
        top:85px;
        right:30px;
        width:11%;
        z-index:115;
        font-size:1.5em;
        border:1px #666 solid;
        -webkit-box-shadow:0 0 10px 0 rgba(255,255,255,1);
        -moz-box-shadow:0 0 10px 0 rgba(255,255,255,1);
        box-shadow:0 0 10px 0 rgba(255,255,255,1);
        padding:8px}
      div#bandcampbackground{
        position:absolute;
        border:2px #fff dotted;
        background:#fff;
        height:100%;
        width:100%;
        opacity:0.2;
        top:-2px;
        left:-2px;
        z-index:114}
      div#bandcampcontent{
        position:relative;
        z-index:116;
        text-shadow:0 0 10px rgba(255,255,255,1)}
      a.floatlink,p{
        color:#FFF!important}
      .file_button,.file_description,span.artist{
        display:none!important}
    </style>
    <title>
      Music
    </title>
  </head>
  <body>
    <div class="greenpage">
    </div>
    <div class="fh">
      <a href="/" id="tl">
        <i>
          futuramerlin
        </i>
      </a>
    </div>
    <div id="content">
      <h1>
        &nbsp;
      </h1>
      <!-- 
</title>
</head>
<body>
<div class="greenpage">
</div>
<div class="fh">
<a href="/" id="tl">
<i>
futuramerlin
</i>
</a>
</div>
<div id="content">
<h1>
Music
</h1>
-->
  <script type="text/javascript">
    function getElementsByClassName(classname, node) {
      if(!node) node = document.getElementsByTagName("body")[0];
      var a = [];
      var re = new RegExp('\\b' + classname + '\\b');
      var els = node.getElementsByTagName("*");
      for(var i=0,j=els.length; i<j; i++)  if(re.test(els[i].className))a.push(els[i]);
      return a;
    }
    function hideAll(){
      var elements = new Array();
      elements = getElementsByClassName('musicbig');
      for(i in elements ){
        elements[i].style.display = "none";
      }
    }
    function hideAllMusic(){
      var elements = new Array();
      elements = getElementsByClassName('audioplayer');
      for(i in elements ){
        elements[i].style.display = "none";
      }
    }
    
  </script>
  <div id="blackbackground" style="background-color:black;z-index:75;position:fixed;top:0px;left:0px;width:100%;height:100%;padding-bottom:80px;">
  </div>
  <div id="musicwrapper" class="flex-hcc">
    <div id="musicthumbcontainer">
      <div id="musicthumbnails">
EOD;
$fragmentA = <<<'EOD'
<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><style type="text/css" media="all">@font-face{font-family:'anoeyfuturamerlincommedium';src:url('d/f/anoeyfuturamerlincom2.61.eot');src:url('d/f/anoeyfuturamerlincom2.61.eot?#iefix') format('embedded-opentype'),url('d/f/anoeyfuturamerlincom2.61.woff') format('woff'),url('d/f/anoeyfuturamerlincom2.61.ttf') format('truetype'),url('d/f/anoeyfuturamerlincom2.61.svg#anoeyfuturamerlincommedium') format('svg');font-weight:normal;font-style:normal}@font-face{font-family:'wreatherweb';src:url('d/f/wreathe-r.eot');src:url('d/f/wreathe-r.eot?#iefix') format('embedded-opentype'),url('d/f/wreathe-r.woff') format('woff'),url('d/f/wreathe-r.ttf') format('truetype'),url('d/f/wreathe-r.svg#wreatherweb') format('svg');font-weight:normal;font-style:normal}@font-face{font-family:'wreatherweb';src:url('d/f/wreathe-b.eot');src:url('d/f/wreathe-b.eot?#iefix') format('embedded-opentype'),url('d/f/wreathe-b.woff') format('woff'),url('d/f/wreathe-b.ttf') format('truetype'),url('d/f/wreathe-b.svg#wreathebold') format('svg');font-weight:bold;font-style:normal}@font-face{font-family:'wreatherweb';src:url('d/f/wreathe-i.eot');src:url('d/f/wreathe-i.eot?#iefix') format('embedded-opentype'),url('d/f/wreathe-i.woff') format('woff'),url('d/f/wreathe-i.ttf') format('truetype'),url('d/f/wreathe-i.svg#wreatheitalic') format('svg');font-weight:normal;font-style:italic}@font-face{font-family:'wreatherweb';src:url('d/f/wreathe-bi.eot');src:url('d/f/wreathe-bi.eot?#iefix') format('embedded-opentype'),url('d/f/wreathe-bi.woff') format('woff'),url('d/f/wreathe-bi.ttf') format('truetype'),url('d/f/wreathe-bi.svg#wreathebold_italic') format('svg');font-weight:bold;font-style:italic}html{background:#d5e3cf;background:-webkit-gradient(radial,50% 0,0,50% 0,300,from(#FFF),to(#d5e3cf));background:-moz-radial-gradient(50% 0 90deg,circle farthest-side,#FFF,#d5e3cf,#d5e3cf 31.25em);background-repeat:no-repeat;background-color:#d5e3cf}body{text-align:justify;overflow-x:hidden;min-height:18.75em}form{display:inline}pre,blockquote,li,table,tbody,tr,td,ul,ol{color:#22520f;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;table-layout:fixed}a{color:#520f22;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:none;-webkit-transition:all .8s ease-out;-moz-transition:all .8s ease-out;-o-transition:all .8s ease-out;transition:all .8s ease-out}a:hover{color:#520f22;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:underline}p{color:#22520f;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;word-wrap:break-word;text-indent:30pt;text-align:justify;margin-top:0;margin-bottom:0}table{border-color:transparent}input{background-color:transparent}h1{color:#22520f;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;font-weight:normal;text-align:center}h2,h3,h4,h5,h6{color:#22520f;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;font-weight:normal;font-style:italic;text-align:center}.t{border:0;background-color:transparent;padding:0;overflow:visible;font-size:1em;color:#520f22;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif}.t:hover{border:0;background-color:transparent;padding:0;overflow:visible;font-size:1em;color:#520f22;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:underline;cursor:pointer}div.floattb{position:fixed;bottom:0;left:0;width:100%;z-index:3;text-align:center}div.floatbg{left:25px;right:25px;position:fixed;bottom:0;height:25px;z-index:2;opacity:.85;background-color:#f0f0f0}a.floatlink{font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:none}a.floatlink:hover{font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:underline}div.generated-toc{text-align:left;list-style-type:none;position:fixed;bottom:25px;left:35px;width:25%;z-index:2;background-color:#efefef;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:none}div#hideshow{position:fixed;bottom:0;left:-12px;width:10%;z-index:4;text-align:left}div#generated-toc a{font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:none;overflow-y:scroll}div#generated-toc ul{text-indent:-10pt;list-style-type:none;font-size:x-small}a#generated_toc_d_toggle:hover{text-decoration:none}p#toggle-container{text-align:left}div.greenpage{position:absolute;top:0;min-height:18.75em;background-color:transparent;margin:8px;margin-right:8px;z-index:-1}div.fh{left:25px;right:25px;position:absolute;top:25px;height:25px;z-index:100;text-align:left;font-size:2em;}div.litem{padding:10px;text-align:center}div.smalllink{padding-top:20px;text-align:center;font-size:x-small}div.relative{position:relative;padding-top:0}.reveal-modal-bg{position:fixed;height:100%;width:100%;background:#000;background:rgba(0,0,0,.8);z-index:100;display:none;top:0;left:0}.reveal-modal{visibility:hidden;top:75px;left:0;margin-left:-10px;width:90%;max-width:900px;background:#eee url(g.png) no-repeat -200px -80px;position:absolute;z-index:101;padding:0;-moz-border-radius:5px;-webkit-border-radius:5px;border-radius:5px;-moz-box-shadow:0 0 10px rgba(0,0,0,.4);-webkit-box-shadow:0 0 10px rgba(0,0,0,.4);-box-shadow:0 0 10px rgba(0,0,0,.4)}.reveal-modal .close-reveal-modal{font-size:22px;line-height:.5;position:absolute;top:8px;right:11px;color:#aaa;font-weight:bold;cursor:pointer}div.logobox{margin:auto;display:inline-block;position:relative;height:20%;width:auto;padding-top:16px;padding-left:24px;float:left;padding-right:75px}div.holder{-webkit-box-shadow:0 0 10px 5px #FFF;box-shadow:0 0 10px 0 #FFF;margin:auto;position:relative;left:25px;width:322px;display:inline-block}div.caption{color:white;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:none}div.captionbg{position:absolute;bottom:0;left:0;width:100%;height:100%;background:#000;background:url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEgMSIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSI+CiAgPGxpbmVhckdyYWRpZW50IGlkPSJncmFkLXVjZ2ctZ2VuZXJhdGVkIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgeDE9IjAlIiB5MT0iMCUiIHgyPSIwJSIgeTI9IjEwMCUiPgogICAgPHN0b3Agb2Zmc2V0PSI5MCUiIHN0b3AtY29sb3I9IiMwMDAwMDAiIHN0b3Atb3BhY2l0eT0iMSIvPgogICAgPHN0b3Agb2Zmc2V0PSI5MCUiIHN0b3AtY29sb3I9IiMwMDAwMDAiIHN0b3Atb3BhY2l0eT0iMSIvPgogICAgPHN0b3Agb2Zmc2V0PSIxMDAlIiBzdG9wLWNvbG9yPSIjNjA2MDYwIiBzdG9wLW9wYWNpdHk9IjEiLz4KICAgIDxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzI2MjYyNiIgc3RvcC1vcGFjaXR5PSIxIi8+CiAgPC9saW5lYXJHcmFkaWVudD4KICA8cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iMSIgaGVpZ2h0PSIxIiBmaWxsPSJ1cmwoI2dyYWQtdWNnZy1nZW5lcmF0ZWQpIiAvPgo8L3N2Zz4=");background:-moz-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background:-webkit-gradient(linear,left top,left bottom,color-stop(90%,rgba(0,0,0,1)),color-stop(90%,rgba(0,0,0,1)),color-stop(100%,rgba(40,40,40,1)),color-stop(100%,rgba(38,38,38,1)));background:-webkit-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background:-o-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background:-ms-linear-gradient(top,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);background:linear-gradient(to bottom,rgba(0,0,0,1) 90%,rgba(0,0,0,1) 90%,rgba(40,40,40,1) 100%,rgba(38,38,38,1) 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#000000',endColorstr='#282828',GradientType=0);opacity:.8;z-index:-1}img.logo{border-right:1px solid #666;border-left:1px solid #666;border-top:1px solid #666;z-index:105}div.caption:hover{color:white;font-family:'wreatherweb','Wreathe','Centaur MT Std','Centaur MT','Centaur',serif;text-decoration:underline}a.captionlink{color:white}div#paddingbottom{height:16px}div#content{position:static;padding-top:55px;padding-bottom:35px}div#page{position:absolute;left:50%;width:640px;margin-left:-320px}a:focus,a:active,button,input[type="reset"]::-moz-focus-inner,input[type="button"]::-moz-focus-inner,input[type="submit"]::-moz-focus-inner,select::-moz-focus-inner,input[type="file"]>input[type="button"]::-moz-focus-inner{outline:none !important}.popuplink{color:white !important}a#tl{font-size:.93em !important; padding-left: .7em; padding-top:1.2em;}</style><style type="text/css" media="all">a#tl{color:#FFF!important;z-index:110!important}div.floattb{z-index:110!important;color:#FFF!important}div{color:#fff!important;font-family:wreatherweb,Wreathe,'Centaur MT Std','Centaur MT',Centaur,serif;word-wrap:break-word;margin-top:0;margin-bottom:0;text-align:left}table,tbody,tr,td{color:#fff;font-family:wreatherweb,Wreathe,'Centaur MT Std','Centaur MT',Centaur,serif;table-layout:fixed}table{border:1px solid #FFF!important}td{border:1px dotted #FFF!important}div.floatbg{z-index:105!important;opacity:0.25!important}a{color:#aaf!important}p.mdate{padding-bottom:15px}h2{color:#FFF!important;font-style:normal!important}html{min-height:100%!important;position:relative!important}body{height:100%!important;overflow:hidden!important}div#mconstraints{position:fixed;bottom:0;top:0;right:0;left:0}#content{position:fixed!important;bottom:0!important;top:0!important;right:0!important;left:0!important}div#musicwrapper{-webkit-box-shadow:0 0 5px 0 rgba(255,255,255,1);-moz-box-shadow:0 0 5px 0 rgba(255,255,255,1);box-shadow:0 0 5px 0 rgba(255,255,255,1);top:-110px;max-width:680px!important;z-index:100;height:100%;position:relative;margin-left:auto;margin-right:auto;border:1px #888 solid;background-color:#111;width:100%}div#intro{left:0!important}div#musicthumbcontainer{background-color:#333;float:left;max-width:30%;width:180px;z-index:110;position:absolute;top:36px;bottom:0;overflow-y:auto;overflow-x:hidden}div#musicthumbnails{position:relative;bottom:0;height:100%}div.musicbig{display:none;position:absolute;bottom:0;margin-left:30%;top:36px;right:0;background-color:#111;overflow-y:scroll;padding:15px}img.musicbigimage{width:100%;-webkit-box-shadow:0 0 15px 0 rgba(255,255,255,1);-moz-box-shadow:0 0 15px 0 rgba(255,255,255,1);box-shadow:0 0 15px 0 rgba(255,255,255,1)}iframe.musicbigplayer{width:100%;-webkit-box-shadow:0 0 15px 0 rgba(255,255,255,1);border:0;-moz-box-shadow:0 0 15px 0 rgba(255,255,255,1);box-shadow:0 0 15px 0 rgba(255,255,255,1)}img.musicthumb{width:80%;max-width:150px;-webkit-box-shadow:0 0 8px 0 rgba(255,255,255,1);-moz-box-shadow:0 0 8px 0 rgba(255,255,255,1);box-shadow:0 0 8px 0 rgba(255,255,255,1);cursor:pointer;margin:10%}img.musicthumb:hover{border-left:3px #FFF solid}.mlefti{text-align:left!important;padding-left:3px}.audiojs{position:relative;top:0;right:0;width:100%!important}div#bandcamplink{position:fixed;top:85px;right:30px;width:11%;z-index:115;font-size:1.5em;border:1px #666 solid;-webkit-box-shadow:0 0 10px 0 rgba(255,255,255,1);-moz-box-shadow:0 0 10px 0 rgba(255,255,255,1);box-shadow:0 0 10px 0 rgba(255,255,255,1);padding:8px}div#bandcampbackground{position:absolute;border:2px #fff dotted;background:#fff;height:100%;width:100%;opacity:0.2;top:-2px;left:-2px;z-index:114}div#bandcampcontent{position:relative;z-index:116;text-shadow:0 0 10px rgba(255,255,255,1)}a.floatlink,p{color:#FFF!important}.file_button,.file_description,span.artist{display:none!important}</style><title>Music</title></head><body><div class="greenpage"></div><div class="fh"><a href="/" id="tl"><i>futuramerlin</i></a></div><div id="content"><h1>&nbsp;</h1><!-- </title></head><body><div class="greenpage"></div><div class="fh"><a href="/" id="tl"><i>futuramerlin</i></a></div><div id="content"><h1> Music</h1>  --><script type="text/javascript">function getElementsByClassName(classname, node) { if(!node) node = document.getElementsByTagName("body")[0]; var a = []; var re = new RegExp('\\b' + classname + '\\b'); var els = node.getElementsByTagName("*"); for(var i=0,j=els.length; i<j; i++)  if(re.test(els[i].className))a.push(els[i]); return a; } function hideAll(){ var elements = new Array(); elements = getElementsByClassName('musicbig'); for(i in elements ){ elements[i].style.display = "none"; } } function hideAllMusic(){ var elements = new Array(); elements = getElementsByClassName('audioplayer'); for(i in elements ){ elements[i].style.display = "none"; } } </script><div id="blackbackground" style="background-color:black;z-index:75;position:fixed;top:0px;left:0px;width:100%;height:100%;padding-bottom:80px;"></div><div id="musicwrapper" class="flex-hcc"><div id="musicthumbcontainer"><div id="musicthumbnails">
<br>
EOD;

$fragmentB = <<<'EOD'
      </div>
    </div>
    <!-- 
<script src="http://audiocogs.org/dgplayer/resources/classlist.js">
</script>
<script>
var unsupported;
if (!window.Audio || !('mozWriteAudio' in new Audio()) && !window.AudioContext && !window.webkitAudioContext) {
unsupported = true;
document.body.classList.add("unsupported");
}

</script>
<div id="unsupported">
We're really sorry about this, but it looks like your browser doesn't support an Audio API. Please try these demos in Chrome 15+ or Firefox 8+ or watch a 
<a href="http://vimeo.com/33919455">
screencast
</a>
. 
</div>
<script src="http://audiocogs.org/dgplayer/player.js" type="text/javascript">
</script>
<script src="http://audiocogs.org/codecs/js/auroraplayer.js" type="text/javascript">
</script>
<div class="player" id="dgplayer" tabindex="0" style="position:absolute;right:0px;top:-20px;width:68% !important;height:150px !important;">
<div class="avatar">

<img src="http://audiocogs.org/dgplayer/resources/fallback_album_art.png" alt="">
</div>
<span class="title">
Unknown Title
</span>
<span class="artist">
Unknown Artist
</span>
<div class="button">
</div>
<div class="volume">

<img src="http://audiocogs.org/dgplayer/resources/volume_high.png" alt="Volume high">

<div class="track">

<div class="progress">
</div>

<div class="handle">
</div>

</div>

<img src="http://audiocogs.org/dgplayer/resources/volume_low.png" alt="Volume low">
</div>
<div class="seek">

<span>
0:00
</span>

<div class="track">

<div class="loaded">
</div>

<div class="progress">
</div>

</div>

<span>
-0:00
</span>
</div>

<div class="file_button">
</div>
<span class="file_description">
Choose a FLAC file on your computer
</span>
</div>
<script src="http://audiocogs.org/codecs/js/aurora.js">
</script>
<script src="http://audiocogs.org/codecs/js/flac.js">
</script>
<script type="text/javascript">
// Chrome doesn't support changing the sample rate, and uses whatever the hardware supports. // We cheat here. Instead of resampling on the fly, we're currently just loading two different // files based on common hardware sample rates. var _sampleRate = (function() { var AudioContext = (window.AudioContext || window.webkitAudioContext); if (!AudioContext)  return 44100;  return new AudioContext().sampleRate; }()); (function(DGPlayer){ if (unsupported) return;  DGPlayer.volume = 100;  var player, onplay; var url = '';  DGPlayer.on('play', onplay = function(){  if (player)  player.disconnect();   player = new DGAuroraPlayer(AV.Player.fromURL(url), DGPlayer);  DGPlayer.off('play', onplay); });  DGPlayer.on('file', function(file) {   if (file) {  if (player)   player.disconnect();    player = new DGAuroraPlayer(AV.Player.fromFile(file), DGPlayer);  DGPlayer.off('play', onplay);  } }); }(DGPlayer(document.getElementById('dgplayer')))); 
</script>
-->
    <div class="musicbig" id="intro" style="display:block !important;">
      <div id="musicbigcontainer">
        <h2>
          Music
        </h2>
        <p class="mdate">
          Choose a release from the column to the left.
          <br>
          <br>
        </p>
      </div>
    </div>
    <div id="musicbigcontainerb">
EOD;
$fragmentB = <<<'EOD'
</div></div><!-- <script src="http://audiocogs.org/dgplayer/resources/classlist.js"></script><script> var unsupported; if (!window.Audio || !('mozWriteAudio' in new Audio()) && !window.AudioContext && !window.webkitAudioContext) { unsupported = true; document.body.classList.add("unsupported"); } </script><div id="unsupported"> We're really sorry about this, but it looks like your browser doesn't support an Audio API. Please try these demos in Chrome 15+ or Firefox 8+ or watch a <a href="http://vimeo.com/33919455">screencast</a>. </div><script src="http://audiocogs.org/dgplayer/player.js" type="text/javascript"></script><script src="http://audiocogs.org/codecs/js/auroraplayer.js" type="text/javascript"></script><div class="player" id="dgplayer" tabindex="0" style="position:absolute;right:0px;top:-20px;width:68% !important;height:150px !important;"><div class="avatar">  <img src="http://audiocogs.org/dgplayer/resources/fallback_album_art.png" alt=""></div><span class="title">Unknown Title</span><span class="artist">Unknown Artist</span><div class="button"></div><div class="volume">  <img src="http://audiocogs.org/dgplayer/resources/volume_high.png" alt="Volume high">  <div class="track">  <div class="progress"></div>  <div class="handle"></div>  </div>  <img src="http://audiocogs.org/dgplayer/resources/volume_low.png" alt="Volume low"></div><div class="seek">  <span>0:00</span>  <div class="track">  <div class="loaded"></div>  <div class="progress"></div>  </div>  <span>-0:00</span></div>  <div class="file_button"></div><span class="file_description">Choose a FLAC file on your computer</span></div><script src="http://audiocogs.org/codecs/js/aurora.js"></script><script src="http://audiocogs.org/codecs/js/flac.js"></script><script type="text/javascript"> // Chrome doesn't support changing the sample rate, and uses whatever the hardware supports. // We cheat here. Instead of resampling on the fly, we're currently just loading two different // files based on common hardware sample rates. var _sampleRate = (function() { var AudioContext = (window.AudioContext || window.webkitAudioContext); if (!AudioContext)  return 44100;  return new AudioContext().sampleRate; }()); (function(DGPlayer){ if (unsupported) return;  DGPlayer.volume = 100;  var player, onplay; var url = '';  DGPlayer.on('play', onplay = function(){  if (player)  player.disconnect();   player = new DGAuroraPlayer(AV.Player.fromURL(url), DGPlayer);  DGPlayer.off('play', onplay); });  DGPlayer.on('file', function(file) {   if (file) {  if (player)   player.disconnect();    player = new DGAuroraPlayer(AV.Player.fromFile(file), DGPlayer);  DGPlayer.off('play', onplay);  } }); }(DGPlayer(document.getElementById('dgplayer')))); </script> --><div class="musicbig" id="intro" style="display:block !important;"><div id="musicbigcontainer"><h2>Music</h2><p class="mdate">Choose a release from the column to the left.<br><br></p></div></div><div id="musicbigcontainerb">
EOD;

$fragmentC = <<<'EOD'
    </div>
    <script src="/d/audio.min.js">
    </script>
    <script>
      audiojs.events.ready(function() {
        var as = audiojs.createAll();
      }
                          );
      
    </script>
    <audio id="featuredaudio" src="/d/p/main/1._The_Truthteller__rev._3-2_.mp3" preload="auto" class="audioplayer">
      Could not start audio player. Does your browser support it?
    </audio>
  </div>
  <div id="bandcamplink">
    <div id="bandcampcontent">
      My music is available here for free, but if you want to buy it, 
      <a href="http://futuramerlin-com.bandcamp.com/" target="_blank">
        head over to Bandcamp
      </a>
      !
    </div>
    <div id="bandcampbackground">
    </div>
  </div>
  </div>
  </body>
</html>
EOD;

$fragmentC = <<<'EOD'
</div><script src="/d/audio.min.js"></script><script> audiojs.events.ready(function() { var as = audiojs.createAll(); }); </script><audio id="featuredaudio" src="/d/p/main/1._The_Truthteller__rev._3-2_.mp3" preload="auto" class="audioplayer">Could not start audio player. Does your browser support it?</audio></div><div id="bandcamplink"><div id="bandcampcontent">My music is available here for free, but if you want to buy it, <a href="http://futuramerlin-com.bandcamp.com/" target="_blank">head over to Bandcamp</a>!</div><div id="bandcampbackground"></div></div></div>
  </div>
  </body>
</html>
EOD;
/*
     <div class="musicbig" id="bonnousuper">
        <h2>
          [TITLE]
        </h2>
        Date: [DATE]
        <br>
        Tracks:
        <br>
        <table style="text-align: left; width: 100%;" border="1" >
          <tbody>
            <tr>
              <td style="vertical-align: top;">
                #
                <br>
              </td>
              <td style="vertical-align: top;">
                &#128266;
              </td>
              <td style="vertical-align: top;">
                Track name
                <br>
              </td>
              <td style="vertical-align: top;">
                Duration
                <br>
              </td>
            </tr>
            <tr>
              <td style="vertical-align: top;">
                1
              </td>
              <td style="vertical-align: top;">
                <audio src="http://archive.org/download/SpiritTruthSuperCollectionfuturamerlinId5106/1.%20The%20Truthteller%20%5Brev.%203-2%5D.mp3" preload="none" class="audioplayer">
                  Could not start audio player. Does your browser support it?
                </audio>
                <a target="_blank" href="http://archive.org/download/SpiritTruthSuperCollectionfuturamerlinId5106/1.%20The%20Truthteller%20%5Brev.%203-2%5D.flac">
                  Lossless…
                </a>
              </td>
              <td style="vertical-align: top;">
                <a target="_blank" href="d/s/music/t/truthteller.html">
                  The Truthteller [rev. 3-2]
                </a>
                <br>
              </td>
              <td style="vertical-align: top;">
                9.01
                <br>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      */
//        <img class="musicthumb" src="http://futuramerlin.com/d/s/music/r/a/fullmoon/t1.png" alt="Full Moon" onClick="hideAll();document.getElementById('fullmoon').style.display='block';">

$releases = array(
5105 => array('<span style="font-family: anoeyfuturamerlincommedium,AnoeyFuturamerlincom;">&#57359;</span>: The Spirit Truth ~Internet Demos~', 'TheSpiritTruthfuturamerlinId5105', '28 August 2013'),
0 => array("test release", "identifier", "date"),
);

$tracks = array(
array(0, "test track 1", "filename", "3.00"),
array(0, "test track 2", "filename", "3.00"),
array(5105, "The Truthteller [rev. 3-2]", "1.%20The%20Truthteller%20%5Brev.%203-2%5D", "9.01"),
array(5105, "In the Kingdom of the Undead [Version 2, 10 July 2013]", "2.%20In%20the%20Kingdom%20of%20the%20Undead%20%5BVersion%202,%2010%20July%202013%5D", "3.00"),


);
#from http://stackoverflow.com/questions/8781911/remove-non-ascii-characters-from-string-in-php:
#$str = preg_replace('/[[:^print:]]/', '', $str);
echo $fragmentA;
foreach ($releases as $id=>$release) {
echo '<img class="musicthumb" src="https://archive.org/download/'.$release[1].'/t1.png" alt="'.preg_replace('/[^\w]/', ' ', $release[0]).'" onClick="hideAll();document.getElementById(\'release'.$id.'\').style.display=\'block\';">';
}

echo $fragmentB;
foreach ($releases as $id=>$release) {
echo '     <div class="musicbig" id="release'.$id.'">
        <h2>'.$release[0].'</h2>
        Date: '.$release[2].'
        <br>
        Tracks:
        <br>
        <table style="text-align: left; width: 100%;" border="1" >
          <tbody>
            <tr>
              <td style="vertical-align: top;">
                #
                <br>
              </td>
              <td style="vertical-align: top;">
                &#128266;
              </td>
              <td style="vertical-align: top;">
                Track name
                <br>
              </td>
              <td style="vertical-align: top;">
                Duration
                <br>
              </td>
            </tr>';

foreach($tracks as $track) {
$i = 0;
	if($track[0] == $id) {
	$i = $i+1;
	echo '<tr>
              <td style="vertical-align: top;">
                '.$i.'
              </td>
              <td style="vertical-align: top;">
                <audio src="http://archive.org/download/'.$release[1].'/'.$track[2].'.mp3" preload="none" class="audioplayer">
                  Could not start audio player. Does your browser support it?
                </audio>
                <a target="_blank" href="http://archive.org/download/'.$release[1].'/'.$track[2].'.flac">
                  Lossless…
                </a>
              </td>
              <td style="vertical-align: top;">
                  '.$track[1].'
              </td>
              <td style="vertical-align: top;">
                '.$track[3].'
              </td>
            </tr>';
	}
}

echo ' </tbody> </table> </div>';
}
echo $fragmentC;

?>