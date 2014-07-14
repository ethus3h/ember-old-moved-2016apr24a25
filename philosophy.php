<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css" media="all">
@font-face {font-family:"Lato";font-style:normal;font-weight:100;src: local("Lato Hairline"),local("Lato-Hairline"),url(/d/f/lh.woff) format("woff");}
body,p {
	text-align:center;
	font-family:'Lato';
	font-size:1.7rem;
}
h1 {
	font-family:'Lato';
	font-size:3rem;
}
h2 {
	font-family:'Lato';
	font-size:2.7rem;
}
div {
	font-family:'Lato';
	margin:2rem;
}
div.postulate {
	border:0.05rem solid gray;
	/*help from http://stackoverflow.com/questions/5664877/creating-spacing-between-an-element-and-its-border */
	padding:0.3rem;
	font-size:2rem;
}
div.conclusion {
	border:0.05rem solid gray;
	padding:0.3rem;
	font-size:2rem;
}
a {
	text-decoration:none;
	color:#666666;
}
a.pr{
	color:#853030;
}
a.pr:hover {
	text-decoration:underline;
}
span.ornament {
	color:#777777;
	font-size:2.35rem;
}
</style>
<title>Philosophy</title></head>
<body>
<?php
$postulates=array();
function p($id,$caption) {
	global $postulates;
	$postulates[$id] = $caption;
	echo '<div class="postulate" id="'.$id.'"><a href="#'.$id.'">'.$caption.'</a></div>';
}
function b($id,$ending="solo") {
	global $postulates;
	if($ending===true) {
		$affix=', ';
	}
	if($ending==="solo") {
		$affix=', ';
	}
	if($ending==="false") {
		$affix=', and ';
	}
	echo '<a class="pr" href="#'.$id.'">'.substr(strtolower($postulates[$id]),0,-1).'</a>'.$affix;
}
function c($id,$caption) {
	echo '<div class="conclusion" id="'.$id.'"><a href="#'.$id.'">'.$caption.'</a></div>';	
}
function nc() {
	echo 'Because ';
}
function ec() {
	echo '<span class="ornament">~ &#x2744; ~</span>';
}
?>
<h1>Some Philosophical Premises:</h1>
<hr>
<p>
<?php
p('pcr','Sometimes one right will conflict with another.');
p('pllh','People have a right to life, liberty, and happiness.');
?>
</p>
<hr>
<h1>On the basis of these assumptions, I conclude that:</h1>
<hr>
<p>
<?php
nc();
b('pcr');
c('cec','It is sometimes difficult for a person, government, society, or other entity to choose an ethical course of action.');
ec();

nc();
b('pllh');
c('ctr','People have a right to a tranquil society.');
ec();


?>
</p>
</body>
</html>