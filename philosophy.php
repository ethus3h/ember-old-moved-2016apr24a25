<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css" media="all">
@font-face {font-family:"Lato";font-style:normal;font-weight:100;src: local("Lato Hairline"),local("Lato-Hairline"),url(/d/f/lh.woff) format("woff");}
body,p {
	text-align:center;
}
h1 {
	font-family:'Lato';
	font-size:3em;
}
h2 {
	font-family:'Lato';
	font-size:2.7em;
}
div {
	font-family:'Lato';
	margin:2em;
}
div.postulate {
	border:0.05em solid gray;
	/*help from http://stackoverflow.com/questions/5664877/creating-spacing-between-an-element-and-its-border */
	padding:0.3em;
	font-size:2em;
}
div.conclusion {
	border:0.05em solid gray;
	padding:0.3em;
	font-size:2em;
}
a {
	text-decoration:none;
	color:#666666;
}
</style>
<title>Philosophy</title></head>
<body>
<?php
function addPostulate($id,$caption) {
	echo '<div class="postulate" id="'.$id.'"><a href="#'.$id.'">'.$caption.'</a></div>';
}
function referencePostulate($id) {
	
}
function addConclusion($id,$caption) {
	echo '<div class="conclusion" id="'.$id.'"><a href="#'.$id.'">'.$caption.'</a></div>';	
}
?>
<h1>Some Philosophical Premises</h1>
<hr>
<p>
<?php
addPostulate('pcr','Sometimes peoples\' rights will conflict with other rights.');
addPostulate('ptr','People have a right to a tranquil society.');
?>
</p>
<hr>
<h2>On the basis of these assumptions, I conclude that:</h2>
<p>
<?php
addConclusion('cec','It is sometimes difficult for a person, government, society, or other entity to choose an ethical course of action.')
?>
</p>
</body>
</html>