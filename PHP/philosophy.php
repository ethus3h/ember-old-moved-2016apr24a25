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
	background-color:#DDDDFF;
}
div.conclusion {
	border:0.05rem solid gray;
	padding:0.3rem;
	font-size:2rem;
	background-color:#BBFFBB;
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
$currentcid = '';
function p($id,$caption) {
	global $postulates;
	$postulates[$id] = $caption;
	echo '<div class="postulate" id="'.$id.'"><a href="#'.$id.'">'.$caption.'</a></div>';
}
function b($id,$ending="solo",$conclusion=false) {
	global $postulates;
	if($ending===true) {
		$affix=', ';
	}
	if($ending==="solo") {
		$affix=', ';
	}
	if($ending===false) {
		$affix=' and ';
	}
	echo '<a class="pr" href="#'.$id.'">'.substr(strtolower($postulates[$id]),0,-1).'</a>'.$affix;
}
function bc($id,$ending="solo") {
	b($id,$ending,true);
}
function c($caption) {
	global $currentcid;
	global $postulates;
	$postulates[$currentcid] = $caption;
// 	print_r($postulates);
	echo '<div class="conclusion"><a href="#'.$currentcid.'">'.$caption.'</a></div>';	
}
function nc($id) {
	global $currentcid;
	$currentcid = $id;
	echo '<span id="'.$id.'">Because</span> ';
}
function ec() {
	echo '<span class="ornament">~ &#x2744; ~</span><br><br>';
}
?>
<h1>Some Philosophical Premises:</h1>
<hr>
<p>
<?php
p('pc','Sometimes one premise will conflict with another.');
p('pf','People have a right to freedom.');
p('pwhy','People\'s beliefs are based on their perceptions/experiences.');
?>
</p>
<hr>
<h1>On the basis of these assumptions, I conclude that:</h1>
<hr>
<p>
<?php
nc('copinion');
b('pwhy');
c('People have opinions that differ from one another.');
ec();

nc('cnoreality');
b('copinion');
c('There is no one universal reality that holds true for all people.');
ec();

nc('cmanipulable');
b('pwhy');
c('People\'s beliefs and opinions can easily be manipulated by other people.');
ec();

nc('ctruth');
b('copinion');
c('Truth is not absolute, and is known through the finding of consensus between people.');
ec();

nc('cec');
b('pc');
c('It is sometimes difficult for a person, government, society, or other entity to choose an ethical course of action.');
ec();

nc('cl');
b('pf');
c('People have a right to their physical and mental wellbeing.');
ec();

nc('cg');
b('pf');
c('An institution and social contract should be established by the consensus of society to protect people\'s freedom.');
ec();

nc('cgr');
b('pf',false);
bc('cg');
c('If the institution and social contract thus established fail to fulfill their purpose of protecting people\'s freedom, or act in a manner not in accordance with this objective, they may be discarded and replaced at the discretion of the consensus of society.');
ec();

nc('co');
b('pf',false);
bc('cg');
c('A person may opt out of the institution and social contract thus established, and in doing so sacrifice any protection promised by them.');
ec();

nc('ctr');
b('pf');
c('People have a right to a tranquil society.');
ec();


?>
</p>
</body>
</html>