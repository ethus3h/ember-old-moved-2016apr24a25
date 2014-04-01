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
//from http://tzi.fr/js/snippet/convert-em-in-px


function getRootElementEmSize(){return parseFloat(getComputedStyle(document.documentElement).fontSize);}
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
	vptattach: The vertical position on the anchor box at which to attach this box to the anchor box.
	vptattunit: The units of vptattach. Only % or px allowed.
	vpos: Vertical position of this box relative to the vpanchor box.
	vposunit: Units (%, or possibly rem?) of vpos
	vconattach: The box to constrain this box's vertical size to.
	vconstrain: Whether to keep the box's vertical size within the box specified in vconattach.
	hpanchor: ID of the box to which this box's horizontal postion should be relative. ID 0 is the browser window.
	hpattach: The horizontal position at which to attach this box to the anchor box.
	hpattunit: The units of hpattach. Only % or px allowed.
	hptattach: The horizontal position on the anchor box at which to attach this box to the anchor box.
	hptattunit: The units of hptattach. Only % or px allowed.
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
	this.vptattach = 0;
	this.vptattunit = "%";
	this.vpos = 0;
	this.vposunit = "%";
	this.vconattach = 0;
	this.vconstrain = true;
	this.hpanchor = 0;
	this.hpattach = 0;
	this.hpattunit = "%";
	this.hptattach = 0;
	this.hptattunit = "%";
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
	if(typeof set["vptattach"] !== "undefined") { this.vptattach = set["vptattach"];}
	if(typeof set["vptattunit"] !== "undefined") { this.vptattunit = set["vptattunit"];}
	if(typeof set["vpos"] !== "undefined") { this.vpos = set["vpos"];}
	if(typeof set["vposunit"] !== "undefined") { this.vposunit = set["vposunit"];}
	if(typeof set["vconattach"] !== "undefined") { this.vconattach = set["vconattach"];}
	if(typeof set["vconstrain"] !== "undefined") { this.vconstrain = set["vconstrain"];}
	if(typeof set["hpanchor"] !== "undefined") { this.hpanchor = set["hpanchor"];}
	if(typeof set["hpattach"] !== "undefined") { this.hpattach = set["hpattach"];}
	if(typeof set["hpattunit"] !== "undefined") { this.hpattunit = set["hpattunit"];}
	if(typeof set["hptattach"] !== "undefined") { this.hpattach = set["hptattach"];}
	if(typeof set["hptattunit"] !== "undefined") { this.hpattunit = set["hptattunit"];}
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
		if(this.wunit == "rem") {
			this.width = this.width * getRootElementEmSize();
			this.wunit = "px";
		}
		if(this.hunit == "rem") {
			this.heighth = this.heighth * getRootElementEmSize();
			this.hunit = "px";
		}
		if(this.hposunit == "rem") {
			this.hpos = this.hpos * getRootElementEmSize();
			this.hposunit = "px";
		}
		if(this.vposunit == "rem") {
			this.vpos = this.vpos * getRootElementEmSize();
			this.vposunit = "px";
		}
		if(this.hpattunit == "rem") {
			this.hpattach = this.hpattach * getRootElementEmSize();
			this.hpattunit = "px";
		}
		if(this.vpattunit == "rem") {
			this.vpattach = this.vpattach * getRootElementEmSize();
			this.vpattunit = "px";
		}
		if(this.hptattunit == "rem") {
			this.hptattach = this.hptattach * getRootElementEmSize();
			this.hptattunit = "px";
		}
		if(this.vptattunit == "rem") {
			this.vptattach = this.vptattach * getRootElementEmSize();
			this.vptattunit = "px";
		}
		wunitA = this.wunit;
		if(this.wunit == "%") {
			wunitA = "px";
		}
		hunitA = this.hunit;
		if(this.hunit == "%") {
			hunitA = "px";
		}
		hposunitA = this.hposunit;
		if(this.hposunit == "%") {
			hposunitA = "px";
		}
		vposunitA = this.vposunit;
		if(this.vposunit == "%") {
			vposunitA = "px";
		}
		hpattunitA = this.hpattunit;
		if(this.hpattunit == "%") {
			hpattunitA = "px";
		}
		vpattunitA = this.vpattunit;
		if(this.vpattunit == "%") {
			vpattunitA = "px";
		}
		hptattunitA = this.hptattunit;
		if(this.hptattunit == "%") {
			hptattunitA = "px";
		}
		vptattunitA = this.vptattunit;
		if(this.vptattunit == "%") {
			vptattunitA = "px";
		}
		if(this.hunit == "relative") {
			hunitA = wunitA;
		}
		tComputedWidth = this.width;
		tComputedHeighth = this.heighth;
		tComputedHpos = $(getAnchor(this.hpanchor)).position().left + this.hpos;
		tComputedVpos = $(getAnchor(this.vpanchor)).position().top + this.vpos;
		tComputedHpa = tComputedHpos;
		tComputedVpa = tComputedVpos;
		//Calculate the width
		computedWidth = this.width+wunitA;
		if(this.wunit == '%') {
			tComputedWidth = $(getAnchor(this.wanchor)).width() * (this.width / 100);
			computedWidth = tComputedWidth+wunitA;
		}
		//Calculate the heighth
		computedHeighth = this.heighth+hunitA;
		if(this.hunit == '%') {
			tComputedHeighth = $(getAnchor(this.hanchor)).height() * (this.heighth / 100);
			computedHeighth = tComputedHeighth+hunitA;
		}
		if(this.hunit == 'relative') {
			tComputedHeighth = tComputedWidth * (this.heighth / 100);
			computedHeighth = tComputedHeighth+hunitA;
		}
		//Calculate the vpos
		computedVpos = tComputedVpos + vposunitA;
		if(this.vposunit == '%') {
			tComputedVpos = ($(getAnchor(this.vpanchor)).position().top + ($(getAnchor(this.vpanchor)).height() * (this.vpos / 100)));
			computedVpos = tComputedVpos+vposunitA;
		}
		//Calculate the hpos
		computedHpos = tComputedHpos + hposunitA;
		if(this.hposunit == '%') {
			tComputedHpos = ($(getAnchor(this.hpanchor)).position().left + ($(getAnchor(this.hpanchor)).width() * (this.hpos / 100)));
			computedHpos = tComputedHpos+hposunitA;
		}
		$(this.anchor).css('height',computedHeighth);
		$(this.anchor).css('width',computedWidth);
		//Calculate the vertical attach point
		computedVpa = this.vpattach;
		if(this.vpattunit == '%') {
			tComputedVpa = tComputedVpos - ($(this.anchor).height() * (this.vpattach / 100));
			computedVpa = tComputedVpa+vposunitA;
		}
		if(this.vptattunit == '%') {
			tComputedVpa = tComputedVpa + ($(getAnchor(this.vpanchor)).height() * (this.vptattach / 100));
			computedVpa = tComputedVpa+vposunitA;
		}
		//Calculate the horizontal attach point
		computedHpa = this.hpattach;
		if(this.hpattunit == '%') {
			tComputedHpa = tComputedHpos - ($(this.anchor).width() * (this.hpattach / 100));
			computedHpa = tComputedHpa+hposunitA;
		}
		if(this.hptattunit == '%') {
			tComputedHpa = tComputedHpa + ($(getAnchor(this.hpanchor)).width() * (this.hptattach / 100));
			computedHpa = tComputedHpa+hposunitA;
		}
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
		if(this.vconstrain==true) {
			if(tComputedVpa < $(getAnchor(this.vconattach)).position().top) {
				//console.log("top constrained");
				vDelta = $(getAnchor(this.vconattach)).position().top - tComputedVpa;
				tComputedHeighth = tComputedHeighth - vDelta;
				tComputedVpa = $(getAnchor(this.vconattach)).position().top;
			}
			if((tComputedVpa + tComputedHeighth) > ($(getAnchor(this.vconattach)).position().top + $(getAnchor(this.vconattach)).height())) {
				//console.log("bottom constrained");
				tComputedHeighth = $(getAnchor(this.vconattach)).height() - (tComputedVpa - $(getAnchor(this.vconattach)).position().top);
			}
		}
		if(this.hconstrain==true) {
			if(tComputedHpa < $(getAnchor(this.hconattach)).position().left) {
				//console.log("left constrained");
				hDelta = $(getAnchor(this.hconattach)).position().left - tComputedHpa;
				tComputedWidth = tComputedWidth - hDelta;
				tComputedHpa = $(getAnchor(this.hconattach)).position().left;
			}
			if((tComputedHpa + tComputedWidth) > ($(getAnchor(this.hconattach)).position().left + $(getAnchor(this.hconattach)).width())) {
				//console.log("right constrained");
				tComputedWidth = $(getAnchor(this.hconattach)).width() - (tComputedHpa - $(getAnchor(this.hconattach)).position().left);
			}
		}
		//recompute relative heighth for constrained boxes
		if(this.hunit == 'relative') {
			if(tComputedWidth > tComputedHeighth) {
				hDifference = (tComputedWidth-tComputedHeighth) / 2;
				tComputedWidth = tComputedHeighth;
				tComputedHpa = tComputedHpa + hDifference;
			}
		}
		if(tComputedHeighth < 3 * getRootElementEmSize()) {
			$(this.anchor).css("font-size",tComputedHeighth);
		}
		computedVpa = tComputedVpa+vposunitA;
		computedHeighth = tComputedHeighth+hunitA;
		computedHpa = tComputedHpa+hposunitA;
		computedWidth = tComputedWidth+wunitA;
		$(this.anchor).css('top',computedVpa);
		$(this.anchor).css('left',computedHpa);
		$(this.anchor).css('height',computedHeighth);
		$(this.anchor).css('width',computedWidth);
	};
	$(this.anchor).css('opacity',"0");
	$(this.anchor).css('display',"block");
	//$(this.anchor).css('z-index',"-1");
	this.compute();
	this.show = function(animation){
		targetElement = this.anchor;
		$(this.anchor).css('z-index',this.zindex);
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
	set["container"] = container;
	set["background"] = "#b7b0b0";
	this.LoadingBg = new FluidBox(set);
	var set = new Object();
	set["container"] = this.LoadingBg.id;
	set["vptattach"] = 100;
	set["vpattach"] = 100;
	set["heighth"] = 0;
	set["hunit"] = "rem";
	this.Loadingremc = new FluidBox(set);
	var set = new Object();
	set["container"] = this.LoadingBg.id;
	set["vpattach"] = 100;
	set["vpanchor"] = this.Loadingremc.id;
	this.LoadingConstraint = new FluidBox(set);
	var set = new Object();
	set["container"] = this.LoadingBg.id;
	set["heighth"] = 75;
	this.LoadingConstraintB = new FluidBox(set);
	var set = new Object();
	set["container"] = this.LoadingBg.id;
	set["vptattach"] = 50;
	set["vpos"] = 1;
	set["vposunit"] = "rem";
	set["vconattach"] = this.LoadingConstraintB.id;
	this.LoadingConstraintC = new FluidBox(set);
	var set = new Object();
	set["contents"] = "Loading...";
	set["container"] = this.LoadingBg.id;
	set["vpanchor"] = this.LoadingBg.id;
	set["vpos"] = 25;
	set["hpanchor"] = this.LoadingBg.id;
	set["hpos"] = 50;
	set["width"] = 100;
	set["wunit"] = "%";
	set["heighth"] = 25;
	set["vpattach"] = 0;
	set["hpattach"] = 50;
	set["css"] = "font-size:3rem;font-family:'Lato',sans-serif;color:#444444;display:flex;align-items:center;flex-flow:column";
	this.LoadingBox = new FluidBox(set);
	var set = new Object();
	set["container"] = this.LoadingBox.id;
	set["vpanchor"] = this.LoadingBox.id;
	set["vpos"] = 100;
	set["vposunit"] = "%";
	set["hpanchor"] = this.LoadingBg.id;
	set["hpos"] = 50;
	set["heighth"] = 100;
	set["width"] = 100;
	set["hunit"] = "relative";
	set["vpattach"] = 0;
	set["hpattach"] = 50;
	this.LoadingCase = new FluidBox(set);
	var set = new Object();
	set["container"] = this.LoadingBox.id;
	set["vpanchor"] = this.LoadingBox.id;
	set["vpos"] = 100;
	set["vconattach"] = this.LoadingConstraintC.id;
	set["hpanchor"] = this.LoadingBox.id;
	set["hpos"] = 50;
	set["wanchor"] = this.LoadingBox.id;
	set["width"] = 100;
	set["wunit"] = "%";
	set["heighth"] = 100;
	set["hunit"] = "relative";
	set["vpattach"] = 0;
	set["hpattach"] = 50;
	set["css"] = "margin-left:auto;margin-right:auto;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;-ms-box-sizing:border-box;box-sizing:border-box;display:block;width:100%;height:100%;margin:auto;border-width:0.1rem;border-style:solid;border-color:#444444 transparent transparent;border-radius:50%;-webkit-animation:spin 2.2s linear infinite;animation:spin 2.2s linear infinite";
	this.LoadingSpinner = new FluidBox(set);
	this.show = function() {
		this.LoadingSpinner.show("none");
		this.LoadingCase.show("none");
		this.LoadingBox.show("none");
		this.LoadingBg.show("fade");
	}
}