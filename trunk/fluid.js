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
function showGroup(group,animation) {
	/* group is the parameter that was passed to the FluidBox when it was instantiated */
	for (var i = 1; i < AllBoxes.length; i = i + 1 ) {
		if(AllBoxes[i]!=undefined) {
			if(AllBoxes[i].group == group) {
				AllBoxes[i].show(animation);
			}
		}
	}
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
		if(AllBoxes[i]!=undefined) {
			AllBoxes[i].compute();
		}
	}
};


//TODO: Constrained boxes currently don't work.
//TODO: blur, crop, and zindex are unimplemented.
function FluidBox(set) {
	AllBoxes[AllBoxes.length+1] = this;
	/* HOW TO CREATE A FLUIDBOX (options can be omitted if desired; default values shown):
	var set = new Object();
	set["property"] = "value";
	NewFluidBox = new FluidBox(set); */
	/* ~Explanations of parameters~
	contents: HTML contents of the box. Generally a <svg> tag. This will be displayed on top of the bgcolor.
	background: Background. Can be any CSS background
	blur: Use a blur effect on whatever's behind this box. (This could actually create another box object below the current one with the content as the blurry SVG?) Result should be like this http://jsfiddle.net/3z6ns/ or this http://jsfiddle.net/YgHA8/1/
	opacity: CSS opacity value
	container: ID of the container box into which this box should be inserted. ID 0 is the browser window.
	vpanchor: ID of the box to which this box's vertical postion should be relative. ID 0 is the browser window.
	vpattach: The vertical position at which to attach this box to the anchor box.
	vpattunit: The units of vpattach. Only % or px allowed.
	vpos: Vertical position of this box relative to the vpanchor box.
	vposunit: Units (%, or possibly rem?) of vpos
	vconattach: The box to constrain this box's vertical size to.
	vconstrain: Whether to keep the box's vertical size within the box specified in vconattach.
	hpanchor: ID of the box to which this box's horizontal postion should be relative. ID 0 is the browser window.
	hpattach: The horizontal position at which to attach this box to the anchor box.
	hpattunit: The units of hpattach. Only % or px allowed.
	hpos: Vertical position of this box relative to the hpanchor box.
	hposunit: Units (%, or possibly rem?) of hpos
	hconattach: The box to constrain this box's horizontal size to.
	hconstrain: Whether to keep the box's horizontal size within the box specified in hconattach.
	wanchor: ID of the box to which this box's horizontal postion should be relative. ID 0 is the browser window.
	width: Width of this box relative to the wanchor box.
	wunit: Units (%, or possibly rem?) of width
	hanchor: ID of the box to which this box's heighth should be relative. ID 0 is the browser window.
	heighth: Width of this box relative to the hanchor box.
	hunit: Units (%, relative, or possibly rem?) of heighth. The value "relative" will set the heighth equal to the specified percentage of the computed width.
	crop: ID of the box to which this box should be cropped, if any. Default to 0 (the browser window) (basically that means no cropping).
	group: a class for the div to later be used for grouping divs
	zindex: stacking order of this box. Should this parameter be used? It seems it would probably be better to just have whatever box comes later in the DOM go on top (by getting a dynamically specified z-index)
	css: Any other arbitrary CSS to specify for this box
	*/
	//Default values
	this.contents = "";
	this.background = "rgba(0,0,0,0)";
	this.opacity = 1;
	this.blur = 0;
	this.container = 0;
	this.vpanchor = 0;
	this.vpattach = 0;
	this.vpattunit = "%";
	this.vpos = 0;
	this.vposunit = "%";
	this.vconattach = 0;
	this.vconstrain = true;
	this.hpanchor = 0;
	this.hpattach = 0;
	this.hpattunit = "%";
	this.hpos = 0;
	this.hposunit = "%";
	this.hconattach = 0;
	this.hconstrain = true;
	this.wanchor = 0;
	this.width = 100;
	this.wunit = "%";
	this.hanchor = 0;
	this.heighth = 100;
	this.hunit = "%";
	this.crop = 0;
	this.group = "";
	this.zindex = undefined;
	this.css = "";
	//Override values if provided
	if(typeof set["contents"] !== "undefined") { this.contents = set["contents"];}
	if(typeof set["background"] !== "undefined") { this.background = set["background"];}
	if(typeof set["opacity"] !== "undefined") { this.opacity = set["opacity"];}
	if(typeof set["blur"] !== "undefined") { this.blur = set["blur"];}
	if(typeof set["container"] !== "undefined") { this.container = set["container"];}
	if(typeof set["vpanchor"] !== "undefined") { this.vpanchor = set["vpanchor"];}
	if(typeof set["vpattach"] !== "undefined") { this.vpattach = set["vpattach"];}
	if(typeof set["vpattunit"] !== "undefined") { this.vpattunit = set["vpattunit"];}
	if(typeof set["vpos"] !== "undefined") { this.vpos = set["vpos"];}
	if(typeof set["vposunit"] !== "undefined") { this.vposunit = set["vposunit"];}
	if(typeof set["vconattach"] !== "undefined") { this.vconattach = set["vconattach"];}
	if(typeof set["vconstrain"] !== "undefined") { this.vconstrain = set["vconstrain"];}
	if(typeof set["hpanchor"] !== "undefined") { this.hpanchor = set["hpanchor"];}
	if(typeof set["hpattach"] !== "undefined") { this.hpattach = set["hpattach"];}
	if(typeof set["hpattunit"] !== "undefined") { this.hpattunit = set["hpattunit"];}
	if(typeof set["hpos"] !== "undefined") { this.hpos = set["hpos"];}
	if(typeof set["hposunit"] !== "undefined") { this.hposunit = set["hposunit"];}
	if(typeof set["hconattach"] !== "undefined") { this.hconattach = set["hconattach"];}
	if(typeof set["hconstrain"] !== "undefined") { this.hconstrain = set["hconstrain"];}
	if(typeof set["wanchor"] !== "undefined") { this.wanchor = set["wanchor"];}
	if(typeof set["width"] !== "undefined") { this.width = set["width"];}
	if(typeof set["wunit"] !== "undefined") { this.wunit = set["wunit"];}
	if(typeof set["hanchor"] !== "undefined") { this.hanchor = set["hanchor"];}
	if(typeof set["heighth"] !== "undefined") { this.heighth = set["heighth"];}
	if(typeof set["hunit"] !== "undefined") { this.hunit = set["hunit"];}
	if(typeof set["crop"] !== "undefined") { this.crop = set["crop"];}
	if(typeof set["group"] !== "undefined") { this.group = set["group"];}
	if(typeof set["zindex"] !== "undefined") { this.zindex = set["zindex"];}
	if(typeof set["css"] !== "undefined") { this.css = set["css"];}
	$(getAnchor(this.container)).append("<div id=\""+newId()+"\" class=\""+this.group+"\" style=\"display:none;"+this.css+"\">"+this.contents+"</div>");
	this.id=getId();
	this.anchor = getAnchor(this.id);
	console.debug(this);
	$(this.anchor).css('background',this.background);
	$(this.anchor).css('opacity',this.opacity);
	$(this.anchor).css('background-size','cover');
	$(this.anchor).css('position','fixed');
	this.compute = function() {
		wunitA = this.wunit;
		if((this.wunit == "%") | (this.wunit == "relative")) {
			wunitA = "px";
		}
		hunitA = this.hunit;
		if((this.hunit == "%") | (this.hunit == "relative")) {
			hunitA = "px";
		}
		hposunitA = this.hposunit;
		if((this.hposunit == "%") | (this.hposunit == "relative")) {
			hposunitA = "px";
		}
		vposunitA = this.vposunit;
		if((this.vposunit == "%") | (this.vposunit == "relative")) {
			vposunitA = "px";
		}
		hpattunitA = this.hpattunit;
		if((this.hpattunit == "%") | (this.hpattunit == "relative")) {
			hpattunitA = "px";
		}
		vpattunitA = this.vpattunit;
		if((this.vpattunit == "%") | (this.vpattunit == "relative")) {
			vpattunitA = "px";
		}
		tComputedWidth = this.width;
		tComputedHeighth = this.heighth;
		tComputedHpos = this.hpos;
		tComputedVpos = this.vpos;
		tComputedHpa = tComputedHpos;
		tComputedVpa = tComputedVpos;
		//console.log("computedWidth pre: "+computedWidth);
		//console.log("computedHeighth pre: "+computedHeighth);
		//console.debug(tComputedWidth);
		//console.debug(tComputedHeighth);
		//Calculate the width
		computedWidth = this.width+wunitA;
		if(this.wunit == '%') {
			console.log("wunit % computations");
			tComputedWidth = $(getAnchor(this.wanchor)).width() * (this.width / 100);
			computedWidth = tComputedWidth+wunitA;
		}
		//Calculate the heighth
		computedHeighth = this.heighth+hunitA;
		if(this.hunit == '%') {
			console.log("hunit % computations");
			tComputedHeighth = $(getAnchor(this.hanchor)).height() * (this.heighth / 100);
			computedHeighth = tComputedHeighth+hunitA;
		}
		console.log("computedWidth medial: "+computedWidth);
		console.log("computedHeighth medial: "+computedHeighth);
		console.log("tComputedWidth medial: "+tComputedWidth);
		console.log("tComputedHeighth medial: "+tComputedHeighth);
		if(this.hunit == 'relative') {
			console.log("heighth relative unit evaluation");
			tComputedHeighth = tComputedWidth * (this.heighth / 100);
			computedHeighth = tComputedHeighth+hunitA;
		}
		//Calculate the vpos
		computedVpos = this.vpos+vposunitA;
		console.log("computedVpos: "+computedVpos);
		if(this.vposunit == '%') {
			console.log("vpos % unit evaluation");
			tComputedVpos = ($(getAnchor(this.vpanchor)).position().top + ($(getAnchor(this.vpanchor)).height() * (this.vpos / 100)));
			computedVpos = tComputedVpos+vposunitA;
		}
		//Calculate the hpos
		computedHpos = this.hpos+hposunitA;
		console.log("computedHpos: "+computedHpos);
		if(this.hposunit == '%') {
			console.log("hpos % unit evaluation");
			tComputedHpos = ($(getAnchor(this.hpanchor)).position().left + ($(getAnchor(this.hpanchor)).width() * (this.hpos / 100)));
			computedHpos = tComputedHpos+hposunitA;
		}
		console.log("computedWidth before 1st set: "+computedWidth);
		console.log("computedHeighth before 1st set: "+computedHeighth);
		console.log("tComputedWidth before 1st set: "+tComputedWidth);
		console.log("tComputedHeighth before 1st set: "+tComputedHeighth);
		$(this.anchor).css('height',computedHeighth);
		$(this.anchor).css('width',computedWidth);
		//Calculate the vertical attach point
		computedVpa = this.vpattach;
		if(this.vpattunit == '%') {
			tComputedVpa = tComputedVpos - ($(this.anchor).height() * (this.vpattach / 100));
			computedVpa = tComputedVpa+vpattunitA;
			console.log("computedVpa: "+computedVpa);
		}
		//Calculate the horizontal attach point
		computedHpa = this.hpattach;
		if(this.hpattunit == '%') {
			tComputedHpa = tComputedHpos - ($(this.anchor).width() * (this.hpattach / 100));
			computedHpa = tComputedHpa+vpattunitA;
			console.log("computedHpa: "+computedHpa);
		}
		console.log("computedWidth 1b: "+computedWidth);
		console.log("computedHeighth 1b: "+computedHeighth);
		console.log("tComputedWidth 1b: "+tComputedWidth);
		console.log("tComputedHeighth 1b: "+tComputedHeighth);
		/*Spec:
		~~~~
		Vertical constraining:
		~~~~
		Top of box is above top of clip target -> Move to top of clip target
		Bottom of box is below bottom of clip target -> Change heighth to (heighth of clip target - (top of box - top of clip target))
		~~~~
		Horizontal constraining:
		~~~~
		Left of box is to the left of left of clip target -> Move to left of clip target
		Right of box is to the right of clip target -> Change width to (width of clip target - (left of box - left of clip target))
		*/
		console.debug(this.vconstrain);
		console.debug(this.hconstrain);
		if(this.vconstrain==true) {
			console.log("vconstrain evaluated as true");
			if(tComputedVpa < $(getAnchor(this.vconattach)).position().top) {
				tComputedVpa = $(getAnchor(this.vconattach)).position().top;
				console.log("Setting vpos of "+this.anchor+" to "+tComputedVpa);
			}
			if((tComputedVpa + tComputedHeighth) > ($(getAnchor(this.vconattach)).position().top + $(getAnchor(this.vconattach)).height)) {
				tComputedHeighth = $(getAnchor(this.vconattach)).height - (tComputedVpa - $(getAnchor(this.vconattach)).position().top);
				console.log("Setting heighth of "+this.anchor+" to "+tComputedHeighth);
			}
		}
		console.log("computedWidth intermediate: "+computedWidth);
		console.log("computedHeighth intermediate: "+computedHeighth);
		console.log("tComputedWidth intermediate: "+tComputedWidth);
		console.log("tComputedHeighth intermediate: "+tComputedHeighth);

		if(this.hconstrain==true) {
			console.log("hconstrain evaluated as true");
			if(tComputedHpa < $(getAnchor(this.hconattach)).position().left) {
				tComputedHpa = $(getAnchor(this.hconattach)).position().left;
				console.log("Setting hpos of "+this.anchor+" to "+tComputedHpa);
			}
			if((tComputedHpa + tComputedWidth) > ($(getAnchor(this.hconattach)).position().left + $(getAnchor(this.hconattach)).width)) {
				tComputedWidth = $(getAnchor(this.hconattach)).width - (tComputedHpa - $(getAnchor(this.hconattach)).position().left);
				console.log("Setting width of "+this.anchor+" to "+tComputedWidth);
			}
		}
		console.log("tComputedWidth stage 2: "+tComputedWidth);
		console.log("tComputedHeighth stage 2: "+tComputedHeighth);
		computedVpa = tComputedVpa+vposunitA;
		computedHeighth = tComputedHeighth+hunitA;
		computedHpa = tComputedHpa+hposunitA;
		computedWidth = tComputedWidth+wunitA;
		console.log("computedWidth: "+computedWidth);
		console.log("computedHeighth: "+computedHeighth);
		$(this.anchor).css('top',computedVpa);
		$(this.anchor).css('left',computedHpa);
		$(this.anchor).css('height',computedHeighth);
		$(this.anchor).css('width',computedWidth);
	};
	$(this.anchor).css('opacity',"0");
	$(this.anchor).css('display',"block");
	this.compute();
	this.show = function(animation){
		targetElement = this.anchor;
		if(animation == "none") {
			$(targetElement).css('display','block');
			$(targetElement).css('opacity',this.opacity);
		}
		if(animation == "zoomhalffade") {
			$(targetElement).css('top',(tComputedHeighth/4)+'px');
			$(targetElement).css('left',(tComputedWidth/4)+'px');
			$(targetElement).css('width',(tComputedWidth/2)+'px');
			$(targetElement).css('height',(tComputedHeighth/2)+'px');
			$(targetElement).css('opacity','0');
			$(targetElement).show();
			bodyWidth = $('body').width();
			bodyHeighth = $('body').height();
			$(targetElement).animate({
				opacity: this.opacity,
				left: computedHpa,
				top: computedVpa,
				width: tComputedWidth+"px",
				height: tComputedHeighth+"px"
			}, 500, "linear");
		}
		if(animation == "fade") {
			$(targetElement).css('opacity','0');
			$(targetElement).show();
			$(targetElement).animate({
				opacity: this.opacity,
			}, 250, "linear");
		}
	};
}
function LoadingScreen(container) {
	var set = new Object();
	set["background"] = "#b7b0b0";
	this.LoadingBg = new FluidBox(set);
	var set = new Object();
	set["contents"] = "Loading...";
	set["container"] = this.LoadingBg.id;
	set["hconstrain"] = false;
	set["vconstrain"] = false;
	set["vpanchor"] = this.LoadingBg.id;
	set["vpos"] = 50;
	set["hpanchor"] = this.LoadingBg.id;
	set["hpos"] = 50;
	set["width"] = 10;
	set["wunit"] = "rem";
	set["heighth"] = 3;
	set["hunit"] = "rem";
	set["css"] = "font-size:3rem;font-family:'Lato',sans-serif;color:#444444;display:flex;align-items:center;flex-flow:column";
	this.LoadingBox = new FluidBox(set);
	//var LoadingSpinner = new FluidBox("", "rgba(0,0,0,0)",1,0,LoadingBox.id,LoadingBox.id,0,"%",135,"%",0,true,LoadingBox.id,50,"%",50,"%",null,false,LoadingBox.id,75,"%",LoadingBox.id,100,"relative",null,"loadingSpinnerContainer",null,"margin-left:auto;margin-right:auto;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;-ms-box-sizing:border-box;box-sizing:border-box;display:block;width:100%;height:100%;margin:auto;border-width:0.1rem;border-style:solid;border-color:#444444 transparent transparent;border-radius:50%;-webkit-animation:spin 2.2s linear infinite;animation:spin 2.2s linear infinite");
	this.show = function() {
		//this.LoadingSpinner.show("none");
		//this.LoadingBox.show("none");
		this.LoadingBg.show("fade");
	}
}
MainLoadingScreen = new LoadingScreen(0);
MainLoadingScreen.show(); 