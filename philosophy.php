<!doctype html><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style type="text/css" media="all">
@font-face {font-family:"Lato";font-style:normal;font-weight:100;src: local("Lato Hairline"),local("Lato-Hairline"),url(/d/f/lh.woff) format("woff");}
body,p {
	text-align:center;
}
h1 {
	font-family:'Lato';
	font-size:4em;
}
h2 {
	font-family:'Lato';
	font-size:3.5em;
}
span {
	font-family:'Lato';
}
span.postulate {
	border:0.05em solid gray;
	/*help from http://stackoverflow.com/questions/5664877/creating-spacing-between-an-element-and-its-border */
	padding:0.3em;
	font-size:2em;
}
</style>
<title>Philosophy</title></head>
<body>
<?php
function addPostulate($id,$caption) {
	echo '<span class="postulate" id="'.$id.'">'.$caption.'</span><br>';
}
function referencePostulate($id) {
	
}
function addConclusion($caption) {
	
}
?>
<h1>Some Philosophical Premises</h1>
<br><br>
<hr>
<br><br>
<p>
<?php
addPostulate('ptest','Test');
?>
</p>
<h2>On the basis of these assumptions, I conclude that:</h2>
<p>
</p>
</body>
</html>