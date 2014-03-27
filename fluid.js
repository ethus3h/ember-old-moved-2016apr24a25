/* JavaScript code for Fluid//Active. */
globalIdsCounter = 1;
function newId()
{
	globalIdsCounter++;
	return 'FluidBox'+globalIdsCounter;
}
function getId()
{
	return 'FluidBox'+globalIdsCounter;
}
function getAnchor(id)
{
	id = String(id).replace("FluidBox","");
	if(id==0) {
		return "body";
	}
	return "#FluidBox"+id;
}
function showGroup(group) {
	/* group is the parameter that was passed to the FluidBox when it was instantiated */
	
}

function Group(name) {
	this.name = name;
	this.showBox = function (element, index, array) {element.show(this.animation);};
	this.show = function(animation) {this.animation = animation; this.boxes.foreach(showBox());};
	var boxes = new Array();
	this.add = function(box) {this.boxes[this.boxes.length+1] = box};
}
var AllBoxes = new Array();

function RecomputeMetrics() {
	for (var i = 1; i < AllBoxes.length; i = i + 1 ) {
		AllBoxes[i].compute();
	}
};



function FluidBox(contents,background,blur,container,vpanchor,vpattach,vpattunit,vpos,vposunit,hpanchor,hpattach,hpattunit,
hpos,hposunit,wanchor,width,wunit,hanchor,heighth,hunit,crop,group,zindex) {
	AllBoxes[AllBoxes.length+1] = this;
	console.debug(AllBoxes);
	/* ~Explanations of parameters~
	contents: HTML contents of the box. Should be the contents of a <svg> tag. This will be displayed on top of the bgcolor.
	background: Background. Can be any CSS background
	blur: Use a blur effect on whatever's behind this box. (This could actually create another box object below the current one with the content as the blurry SVG?) Result should be like this http://jsfiddle.net/3z6ns/ or this http://jsfiddle.net/YgHA8/1/
	container: ID of the container box into which this box should be inserted. ID 0 is the browser window.
	vpanchor: ID of the box to which this box's vertical postion should be relative. ID 0 is the browser window.
	vpattach: The vertical position at which to attach this box to the anchor box.
	vpattunit: The units of vpattach. Only % or px allowed.
	vpos: Vertical position of this box relative to the vpanchor box.
	vposunit: Units (%, or possibly rem?) of vpos
	hpanchor: ID of the box to which this box's horizontal postion should be relative. ID 0 is the browser window.
	hpattach: The horizontal position at which to attach this box to the anchor box.
	hpattunit: The units of hpattach. Only % or px allowed.
	hpos: Vertical position of this box relative to the hpanchor box.
	hposunit: Units (%, or possibly rem?) of hpos
	wanchor: ID of the box to which this box's horizontal postion should be relative. ID 0 is the browser window.
	width: Width of this box relative to the wanchor box.
	wunit: Units (%, or possibly rem?) of width
	hanchor: ID of the box to which this box's heighth should be relative. ID 0 is the browser window.
	heighth: Width of this box relative to the hanchor box.
	hunit: Units (%, relative, or possibly rem?) of heighth. The value "relative" will set the heighth equal to the specified percentage of the computed width.
	crop: ID of the box to which this box should be cropped, if any. Default to 0 (the browser window) (basically that means no cropping).
	group: a class for the div to later be used for grouping divs
	zindex: stacking order of this box. Should this parameter be used? It seems it would probably be better to just have whatever box comes later in the DOM go on top (by getting a dynamically specified z-index)
	*/
	this.contents = contents;
	this.background = background;
	this.blur = blur;
	this.container = container;
	this.vpanchor = vpanchor;
	this.vpattach = vpattach;
	this.vpattunit = vpattunit;
	this.vpos = vpos;
	console.log("Sent vpos: "+vpos);
	console.log("New vpos: "+this.vpos);
	this.vposunit = vposunit;
	this.hpanchor = hpanchor;
	this.hpattach = hpattach;
	this.hpattunit = hpattunit;
	this.hpos = hpos;
	console.log("Sent hpos: "+hpos);
	console.log("New hpos: "+this.hpos);
	this.hposunit = hposunit;
	this.wanchor = wanchor;
	this.width = width;
	this.wunit = wunit;
	this.hanchor = hanchor;
	this.heighth = heighth;
	this.hunit = hunit;
	this.crop = crop;
	this.group = group;
	this.zindex = zindex;
	console.log("Box instantiating. Contents = "+this.contents+", background = "+this.background
	+", blur = "+this.blur+", container = "+this.container+", vpanchor = "+this.vpanchor+", vpattach = "+this.vpattach+", vpattunit = "+this.vpattunit+", vpos = "+this.vpos
	+", vposunit = "+this.vposunit+", hpanchor = "+this.hpanchor+", hpattach = "+this.hpattach+", hpattunit = "+this.hpattunit+", hpos = "+this.hpos
	+", hposunit = "+this.hposunit+", wanchor = "+this.wanchor+", width = "+this.width
	+", wunit = "+this.wunit+", hanchor = "+this.hanchor+", heighth = "+this.heighth
	+", hunit = "+this.hunit+", crop = "+this.crop+", group = "+this.group+", zindex = "+this.zindex);
	/* console.log(getAnchor(this.vpanchor)); */
	/* $(getAnchor(this.vpanchor)).append("<div id=\""+newId()+"\" style=\"background:"+this.background+";height:"+this.heighth+this.hunit";width:"+this.width+this.wunit+";position:relative;left:"+this.hpos+";top:"+this.vpos+"\">"+"</div>"); */
	$(getAnchor(this.container)).append("<div id=\""+newId()+"\" class=\""+this.group+"\" style=\"display:none;\"><svg>"+this.contents+"</svg></div>");
	this.id=getId();
	this.anchor = getAnchor(this.id);
	$("#"+getId()).css('background',this.background);
	$("#"+getId()).css('background-size','cover');
	$("#"+getId()).css('position','fixed');
	this.compute = function() {
		//Calculate the width
		computedWidth = this.width+this.wunit;
		if(this.wunit == '%') {
			tComputedWidth = $(getAnchor(this.wanchor)).width() * (this.width / 100);
			computedWidth = tComputedWidth+'px';
		}
		//Calculate the heighth
		computedHeighth = this.heighth+this.hunit;
		if(this.hunit == '%') {
			tComputedHeighth = $(getAnchor(this.hanchor)).height() * (this.heighth / 100);
			computedHeighth = tComputedHeighth+'px';
		}
		if(this.hunit == 'relative') {
			tComputedHeighth = tComputedWidth * (this.heighth / 100);
			computedHeighth = tComputedHeighth+'px';
		}
		//Calculate the vpos
		computedVpos = this.vpos+this.vposunit;
		if(this.vposunit == '%') {
			tComputedVpos = ($(getAnchor(this.vpanchor)).position().top + $(getAnchor(this.vpanchor)).height()) * (this.vpos / 100);
			computedVpos = tComputedVpos+'px';
		}
		//Calculate the hpos
		computedHpos = this.hpos+this.hposunit;
		if(this.hposunit == '%') {
			tComputedHpos = ($(getAnchor(this.hpanchor)).position().left + $(getAnchor(this.hpanchor)).width()) * (this.hpos / 100);
			computedHpos = tComputedHpos+'px';
		}
		$("#"+getId()).css('height',computedHeighth);
		$("#"+getId()).css('width',computedWidth);
		//$("#"+getId()).css('top',computedVpos);
		//$("#"+getId()).css('left',computedHpos);
		//Calculate the vertical attach point
		computedVpa = this.vpattach;
		if(this.vpattunit == '%') {
			tComputedVpa = $("#"+getId()).height() * (this.vpattach / 100);
			computedVpa = tComputedVpos - tComputedVpa;
		}
		//Calculate the horizontal attach point
		computedHpa = this.hpattach;
		if(this.hpattunit == '%') {
			tComputedHpa = $("#"+getId()).width() * (this.hpattach / 100);
			computedHpa = tComputedHpos - tComputedHpa;
		}
		$("#"+getId()).css('top',computedVpa+"px");
		$("#"+getId()).css('left',computedHpa+"px");
		console.log("Set vpos: "+this.vpos);
		console.log("Partial vpos: "+tComputedVpos);
		console.log("Computed vpos: "+computedVpos);
		console.log("Set hpos: "+this.hpos);
		console.log("Partial hpos: "+tComputedHpos);
		console.log("Computed hpos: "+computedHpos);
		console.log("Set vpa: "+this.vpattach);
		console.log("Partial vpa: "+tComputedVpa);
		console.log("Computed vpa: "+computedVpa);
		console.log("Set vpa: "+this.hpattach);
		console.log("Partial vpa: "+tComputedHpa);
		console.log("Computed vpa: "+computedHpa);
	}
	this.compute();
	this.show = function(animation){
		//console.log("Displaying box "+this.id+"; ID #"+getId());
		targetElement = this.anchor;
		//console.debug($("#"+getId()));
		if(animation == "none") {
			//console.log("Animation: none");
			//console.debug($("#"+getId()));
			$(targetElement).css('display','block');
			//console.debug($("#"+getId()));
		}
		if(animation == "zoom") {
			//console.log("Animation: zoom");
			$(targetElement).css('top',($('body').height()/4)+'px');
			$(targetElement).css('left',($('body').width()/4)+'px');
			$(targetElement).css('width',($('body').width()/2)+'px');
			$(targetElement).css('height',($('body').height()/2)+'px');
			$(targetElement).css('opacity','0');
			$(targetElement).show();
			bodyWidth = $('body').width();
			bodyHeighth = $('body').height();
			$(targetElement).animate({
				opacity: 1,
				left: computedHpos,
				top: computedVpos,
				width: bodyWidth+"px",
				height: bodyHeighth+"px"
			}, 500, "linear");
			//console.debug($("#"+getId()));
			//$(targetElement).css('display','block');
			//console.debug($("#"+getId()));
		}
		if(animation == "fade") {
			//console.log("Animation: fade");
			

			$(targetElement).css('opacity','0');
			$(targetElement).show();
			$(targetElement).animate({
				opacity: 1,
			}, 250, "linear");
			//console.debug($("#"+getId()));
			//$(targetElement).css('display','block');
			//console.debug($("#"+getId()));
		}
		//console.debug($("#"+getId()));
		//$(targetElement).css('display','block');
		//$(targetElement).show();
		//console.debug($("#"+getId()));
		//console.log(targetElement);

	};
}
function LoadingScreen(container) {
	var LoadingBg = new FluidBox("","#b7b0b0",0,0,0,0,"%",0,"%",0,0,"%",0,"%",0,100,"%",0,100,"%",0,null);
	var LoadingBox = new FluidBox("</svg>Loading...", "rgba(0,0,0,0)",0,LoadingBg.id,LoadingBg.id,50,"%",50,"%",LoadingBg.id,50,"%",50,"%",0,10,"%",0,110,"relative","loadingContainer",null);
	var LoadingSpinner = new FluidBox("</svg>Spinner", "rgba(0,0,0,0)",0,LoadingBox.id,LoadingBox.id,0,"%",110,"%",LoadingBox.id,0,"%",100,"%",LoadingBg.id,10,"%",LoadingBg.id,100,"relative","loadingSpinner",null);
	LoadingSpinner.show("none"); 
	LoadingBox.show("none"); 
	LoadingBg.show("zoom"); 
}
LoadingScreen(0);
