/* JavaScript code for Fluid//Active. */

/* TODO:

* FluidArea: 
* FluidRow: Row of boxes in a specific order (have it take care of setting the positions relative to each other). Pass a list of FluidBox config arrays to the constructor. This will need to understand layout element progression order of the configured language. Be able to specify widths and heighths for the inner boxes to override those computed by the automatic layout.
* FluidGrid: Set of dynamically created FluidRows. Move FluidBoxes between FluidRows and adjust the heighths and widths of the rows to fit nicely in a given area. Pass a list of FluidBox config arrays to the constructor. This will need to understand layout element progression order of the configured language. Be able to specify widths and heighths for the inner boxes to override those computed by the automatic layout.
* ComputedValue: an object representing a number to be computed

*/
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
		return "#pageContents";
	}
	return "#FluidBox"+id;
}

//from http://tzi.fr/js/snippet/convert-em-in-px
function getRootElementEmSize(){return parseFloat(getComputedStyle(document.documentElement).fontSize);}
//from http://stackoverflow.com/questions/728360/most-elegant-way-to-clone-a-javascript-object
function clone(obj) {
    // Handle the 3 simple types, and null or undefined
    if (null == obj || "object" != typeof obj) return obj;

    // Handle Date
    if (obj instanceof Date) {
        var copy = new Date();
        copy.setTime(obj.getTime());
        return copy;
    }

    // Handle Array
    if (obj instanceof Array) {
        var copy = [];
        for (var i = 0, len = obj.length; i < len; i++) {
            copy[i] = clone(obj[i]);
        }
        return copy;
    }

    // Handle Object
    if (obj instanceof Object) {
        var copy = {};
        for (var attr in obj) {
            if (obj.hasOwnProperty(attr)) copy[attr] = clone(obj[attr]);
        }
        return copy;
    }

    throw new Error("Unable to copy obj! Its type isn't supported.");
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
var AllNumbers = new Array();

function RecomputeMetrics() {
	for (var i = 1; i < AllNumbers.length; i = i + 1 ) {
		if(AllNumbers[i]!=undefined) {
			AllNumbers[i].compute();
		}
	}
	for (var i = 1; i < AllBoxes.length; i = i + 1 ) {
		if(AllBoxes[i]!=undefined) {
			AllBoxes[i].compute();
		}
	}
};

function ComputedValue(rule) {
	AllNumbers[AllNumbers.length+1] = this;
	//this.rule = 'return ' + rule + ';';
	this.compute = function() {
		//eval(this.rule);
		return this.rule();
	};
	this.compute();
}
//TODO: Constrained boxes currently don't work.
//TODO: blur, crop, and zindex are unimplemented.
function FluidBox(set) {
	AllBoxes[AllBoxes.length+1] = this;
	/* HOW TO CREATE A FLUIDBOX (options can be omitted if desired; default values shown):
	var set = new Object();
	set["property"] = "value";
	NewFluidBox = new FluidBox(set);
	set=null; */
	/* ~Explanations of parameters~
	contents: HTML contents of the box. Generally a <svg> tag. This will be displayed on top of the bgcolor.
	boxes: Array of FluidBox config arrays to use to create more boxes within this one if this has a FluidRow or FluidGrid behavior.
	behaviour: Can be box, row, or grid. Box is default. Row and grid are more complex layout systems to be used with the boxes property.
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
	this.boxes = undefined;
	this.behaviour = "box";
	this.background = "rgba(0,0,0,0)";
	this.border = undefined;
	this.shadow = "";
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
	this.lmar = 0;
	this.rmar = 0;
	this.tmar = 0;
	this.bmar = 0;
	this.lmarunit = "%";
	this.rmarunit = "%";
	this.tmarunit = "%";
	this.bmarunit = "%";
	this.lmarl = 0;
	this.rmarl = 0;
	this.tmarl = 0;
	this.bmarl = 0;
	this.group = "";
	this.zindex = undefined;
	this.css = "";
	this.bordercolor = undefined;
	//Override values if provided
	if(typeof set["contents"] !== "undefined") { this.contents = set["contents"];}
	if(typeof set["boxes"] !== "undefined") { this.boxes = set["boxes"];}
	if(typeof set["behaviour"] !== "undefined") { this.behaviour = set["behaviour"];}
	if(typeof set["background"] !== "undefined") { this.background = set["background"];}
	if(typeof set["border"] !== "undefined") { this.border = set["border"];}
	if(typeof set["shadow"] !== "undefined") { this.shadow = set["shadow"];}
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
	this.vconattach = this.container;
	if(typeof set["vconattach"] !== "undefined") { this.vconattach = set["vconattach"];}
	if(typeof set["vconstrain"] !== "undefined") { this.vconstrain = set["vconstrain"];}
	if(typeof set["hpanchor"] !== "undefined") { this.hpanchor = set["hpanchor"];}
	if(typeof set["hpattach"] !== "undefined") { this.hpattach = set["hpattach"];}
	if(typeof set["hpattunit"] !== "undefined") { this.hpattunit = set["hpattunit"];}
	if(typeof set["hptattach"] !== "undefined") { this.hpattach = set["hptattach"];}
	if(typeof set["hptattunit"] !== "undefined") { this.hpattunit = set["hptattunit"];}
	if(typeof set["hpos"] !== "undefined") { this.hpos = set["hpos"];}
	if(typeof set["hposunit"] !== "undefined") { this.hposunit = set["hposunit"];}
	this.hconattach = this.container;
	if(typeof set["hconattach"] !== "undefined") { this.hconattach = set["hconattach"];}
	if(typeof set["hconstrain"] !== "undefined") { this.hconstrain = set["hconstrain"];}
	if(typeof set["wanchor"] !== "undefined") { this.wanchor = set["wanchor"];}
	if(typeof set["width"] !== "undefined") { this.width = set["width"];}
	if(typeof set["wunit"] !== "undefined") { this.wunit = set["wunit"];}
	if(typeof set["hanchor"] !== "undefined") { this.hanchor = set["hanchor"];}
	if(typeof set["heighth"] !== "undefined") { this.heighth = set["heighth"];}
	if(typeof set["hunit"] !== "undefined") { this.hunit = set["hunit"];}
	if(typeof set["crop"] !== "undefined") { this.crop = set["crop"];}
	if(typeof set["lmar"] !== "undefined") { this.lmar = set["lmar"];}
	if(typeof set["rmar"] !== "undefined") { this.rmar = set["rmar"];}
	if(typeof set["tmar"] !== "undefined") { this.tmar = set["tmar"];}
	if(typeof set["bmar"] !== "undefined") { this.bmar = set["bmar"];}
	if(typeof set["lmarunit"] !== "undefined") { this.lmarunit = set["lmarunit"];}
	if(typeof set["rmarunit"] !== "undefined") { this.rmarunit = set["rmarunit"];}
	if(typeof set["tmarunit"] !== "undefined") { this.tmarunit = set["tmarunit"];}
	if(typeof set["bmarunit"] !== "undefined") { this.bmarunit = set["bmarunit"];}
	this.lmarl = this.container;
	this.rmarl = this.container;
	this.tmarl = this.container;
	this.bmarl = this.container;
	if(typeof set["lmarl"] !== "undefined") { this.lmarl = set["lmarl"];}
	if(typeof set["rmarl"] !== "undefined") { this.rmarl = set["rmarl"];}
	if(typeof set["tmarl"] !== "undefined") { this.tmarl = set["tmarl"];}
	if(typeof set["bmarl"] !== "undefined") { this.bmarl = set["bmarl"];}
	if(typeof set["group"] !== "undefined") { this.group = set["group"];}
	if(typeof set["zindex"] !== "undefined") { this.zindex = set["zindex"];}
	if(typeof set["css"] !== "undefined") { this.css = set["css"];}
	if(typeof set["bordercolor"] !== "undefined") { this.bordercolor = set["bordercolor"];}
	$(getAnchor(this.container)).append("<div id=\""+newId()+"\" class=\""+this.group+"\" style=\"display:none;"+this.css+"\">"+this.contents+"</div>");
	this.id=getId();
	this.anchor = getAnchor(this.id);
	console.debug(this);
	$(this.anchor).css('background',this.background);
	if(typeof this.border !== "undefined") {
		$(this.anchor).css('border',this.border);
	}
	$(this.anchor).css('box-shadow',this.shadow);
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
		if(this.lmarunit == "rem") {
			this.lmar = this.lmar * getRootElementEmSize();
			this.lmarunit = "px";
		}
		if(this.rmarunit == "rem") {
			this.rmar = this.rmar * getRootElementEmSize();
			this.rmarunit = "px";
		}
		if(this.tmarunit == "rem") {
			this.tmar = this.tmar * getRootElementEmSize();
			this.tmarunit = "px";
		}
		if(this.bmarunit == "rem") {
			this.bmar = this.bmar * getRootElementEmSize();
			this.bmarunit = "px";
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
		lmarunitA = this.lmarunit;
		if(this.lmarunit == "%") {
			lmarunitA = "px";
			this.lmar = $(getAnchor(this.lmarl)).width() * (this.lmar / 100);
		}
		rmarunitA = this.rmarunit;
		if(this.rmarunit == "%") {
			rmarunitA = "px";
			this.rmar = $(getAnchor(this.rmarl)).width() * (this.rmar / 100);
		}
		tmarunitA = this.tmarunit;
		if(this.tmarunit == "%") {
			tmarunitA = "px";
			this.tmar = $(getAnchor(this.tmarl)).height() * (this.tmar / 100);
		}
		bmarunitA = this.bmarunit;
		if(this.bmarunit == "%") {
			bmarunitA = "px";
			this.bmar = $(getAnchor(this.bmarl)).height() * (this.bmar / 100);
		}
		/* console.log("lmar: "+this.lmar);
		console.log("rmar: "+this.rmar);
		console.log("tmar: "+this.tmar);
		console.log("bmar: "+this.bmar); */
		if(this.hunit == "relative") {
			hunitA = wunitA;
		}
		if(this.wunit == "relative") {
			wunitA = hunitA;
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
		if(this.wunit == 'relative') {
			tComputedWidth = tComputedHeighth * (this.heighth / 100);
			computedWidth = tComputedWidth+wunitA;
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
				if((tComputedHeighth - vDelta) > 0) {
					tComputedHeighth = tComputedHeighth - vDelta;
				}
				tComputedVpa = $(getAnchor(this.vconattach)).position().top;
			}
			if((tComputedVpa + tComputedHeighth) > ($(getAnchor(this.vconattach)).position().top + $(getAnchor(this.vconattach)).height())) {
				//console.log("bottom constrained");
				tComputedHeighth = $(getAnchor(this.vconattach)).height() - (tComputedVpa - $(getAnchor(this.vconattach)).position().top);
			}
		}
		if(this.hconstrain==true) {
			if(tComputedHpa < $(getAnchor(this.hconattach)).position().left) {
				console.log("left constrained");
				console.log(tComputedHpa + "<" + $(getAnchor(this.hconattach)).position().left);
				hDelta = $(getAnchor(this.hconattach)).position().left - tComputedHpa;
				if((tComputedWidth - hDelta) > 0) {
					tComputedWidth = tComputedWidth - hDelta;
				}
				tComputedHpa = $(getAnchor(this.hconattach)).position().left;
			}
			if((tComputedHpa + tComputedWidth) > ($(getAnchor(this.hconattach)).position().left + $(getAnchor(this.hconattach)).width())) {
				console.log("right constrained");
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
		/* console.log("lmar: "+this.lmar);
		console.log("rmar: "+this.rmar);
		console.log("tmar: "+this.tmar);
		console.log("bmar: "+this.bmar); */
		computedVpa = ((tComputedVpa + parseInt(this.tmar,10))+vposunitA);
		computedHeighth = ((tComputedHeighth - parseInt(this.bmar,10))+hunitA);
		computedHpa = ((tComputedHpa + parseInt(this.lmar,10))+hposunitA);
		computedWidth = ((tComputedWidth - parseInt(this.rmar,10))+wunitA);
		//console.log((tComputedVpa+vposunitA) - computedVpa);
		//console.log((tComputedHeighth+hunitA) - computedHeighth);
		/* console.log(computedVpa);
		console.log(computedHeighth); */
		$(this.anchor).css('top',computedVpa);
		$(this.anchor).css('left',computedHpa);
		$(this.anchor).css('height',computedHeighth);
		$(this.anchor).css('width',computedWidth);
		if(this.behaviour == "row") {
		}
	};
	$(this.anchor).css('opacity',"0");
	$(this.anchor).css('display',"block");
	//$(this.anchor).css('z-index',"-1");
	this.compute();
	this.show = function(animation){
		console.log("Showing "+this.anchor+" to opacity "+this.opacity+" with animation "+animation+"...");
		this.compute();
		if(typeof this.blurryBox !== "undefined") { this.blurryBox.show();}
		targetElement = this.anchor;
		$(this.anchor).css('z-index',this.zindex);
		if(animation == "none") {
			$(targetElement).css('display','block');
			$(targetElement).css('opacity',this.opacity);
		}
		if(animation == "zoomhalffade") {
			//$(targetElement).css('top',(tComputedHeighth/4)+'px');
			//$(targetElement).css('left',(tComputedWidth/4)+'px');
/*			$(targetElement).css('width',(tComputedWidth/2)+'px');
			$(targetElement).css('height',(tComputedHeighth/2)+'px'); */
			$(targetElement).animate({ scale: 0.5 }, 0);
			$(targetElement).css('opacity','0');
			$(targetElement).show();
			bodyWidth = $('#pageContents').width();
			bodyHeighth = $('#pageContents').height();
			$(targetElement).animate({
				opacity: this.opacity,
				//left: computedHpa,
				//top: computedVpa,
				scale: 1
			}, 500, "linear");
/*				width: tComputedWidth+"px",
				height: tComputedHeighth+"px" */
		}
		if(animation == "fade") {
			$(targetElement).css('opacity','0');
			$(targetElement).show();
			$(targetElement).animate({
				opacity: this.opacity,
			}, 250, "linear");
		}
	};
	if(this.blur != 0) {
		//Not working. I'm trying to make a div blur using filter: url(#filterID);, like in this demo http://jsfiddle.net/3z6ns/27/ (but that's a very complicated way of doing it, I'm looking for something simple). My test div is #fluidBox15
		blurPtA='<svg id="blur';
		blurPtB='" xmlns="http://www.w3.org/2000/svg" version="1.1"><defs><filter id="blur';
		blurPtC='" x="0" y="0"><feGaussianBlur in="SourceGraphic" stdDeviation="';
		blurPtD='" /></filter></defs></svg>';
		compiledBlur = blurPtA+newId()+blurPtB+newId()+blurPtC+this.blur+blurPtD;
		console.log(compiledBlur);
		blurId = getId();
		$('#pageContents').append(compiledBlur);
		var tcset = new Object();
		tcset = clone(set);
		tcset["blur"] = 0;
		tcset["container"] = this.container;
		tcset["contents"] = "";
		tcset["background"] = "rgba(0,0,0,0)";
		tcset["border"] = "";
		tcset["shadow"] = "";
		tcset["css"] = "";
		tcset["group"] = "blurcontainer";
		this.blurredContainer = new FluidBox(tcset);
		tcset=null;
		var tset = new Object();
		tset = clone(set);
		tset["container"] = this.blurredContainer.id;
		tset["border"] = "";
		tset["shadow"] = "";
		tset["blur"] = 0;
		tset["wanchor"] = this.blurredContainer.id;
		tset["width"] = 100;
		tset["hpanchor"] = this.blurredContainer.id;
		//tset["hpos"] = 100;
		tset["contents"] = "";
		tset["background"] = "-moz-element(#FluidBox10) no-repeat fixed";
		//tset["background"] = "rgba(255,0,255,0.5)";
		tDataA="filter:url(#blur";
		tDataB=blurId+");";
		tDataC=tDataA+tDataB;
		console.log(tDataC);
		tset["css"] = tDataC;
		tset["group"] = "blurrybox";
		this.blurryBox = new FluidBox(tset);
		tset=null;
		$(this.anchor).appendTo(this.blurredContainer.anchor);
 		this.blurryBox.show("none");
 		this.blurredContainer.show("none");
	}
	if(typeof this.bordercolor !== "undefined") {
		$(this.anchor).css('border-color',this.bordercolor);		
	}
}
function LoadingScreen(container) {
	var set = new Object();
	set["container"] = container;
	set["wanchor"] = container;
	set["hanchor"] = container;
	set["vpanchor"] = container;
	set["hpanchor"] = container;
	set["background"] = "#b7b0b0";
	this.LoadingBg = new FluidBox(set);
	set=null;
	var set = new Object();
	set["container"] = this.LoadingBg.id;
	set["vptattach"] = 100;
	set["vpattach"] = 100;
	set["heighth"] = 0;
	set["hunit"] = "rem";
	this.Loadingremc = new FluidBox(set);
	set=null;
	var set = new Object();
	set["container"] = this.LoadingBg.id;
	set["vpattach"] = 100;
	set["vpanchor"] = this.Loadingremc.id;
	this.LoadingConstraint = new FluidBox(set);
	set=null;
	var set = new Object();
	set["container"] = this.LoadingBg.id;
	set["heighth"] = 75;
	this.LoadingConstraintB = new FluidBox(set);
	set=null;
	var set = new Object();
	set["container"] = this.LoadingBg.id;
	set["vptattach"] = 50;
	set["vpos"] = 1;
	set["vposunit"] = "rem";
	set["vconattach"] = this.LoadingConstraintB.id;
	this.LoadingConstraintC = new FluidBox(set);
	set=null;
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
	set=null;
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
	set=null;
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
	//set["border"] =  "0.1rem solid #444444";
	set["bordercolor"] = "#444444 transparent transparent";
	set["vpattach"] = 0;
	set["hpattach"] = 50;
	set["css"] = "margin-left:auto;margin-right:auto;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;-ms-box-sizing:border-box;box-sizing:border-box;display:block;width:100%;height:100%;margin:auto;border-width:0.1rem !important;border-style:solid !important;border-color:#444444 transparent transparent !important;border-radius:50% !important;-webkit-animation:spin 2.2s linear infinite;animation:spin 2.2s linear infinite";
	this.LoadingSpinner = new FluidBox(set);
	set=null;
	this.show = function() {
		this.Loadingremc.show("none");
		this.LoadingConstraint.show("none");
		this.LoadingConstraintB.show("none");
		this.LoadingConstraintC.show("none");
		this.LoadingSpinner.show("none");
		this.LoadingCase.show("none");
		this.LoadingBox.show("none");
		this.LoadingBg.show("fade");
	}
}
function Panel(container) {
	var set = new Object();
	set["container"] = container;
	set["background"] = "rgba(206,190,232,0.4)";
	set["border"] = "1px solid rgba(255,255,255,0.75)";
	set["shadow"] = "0px 0px 2px #FFFFFF";
	set["blur"] = 2;
	set["vconattach"] = container;
	set["hconattach"] = container;
	this.panelBox = new FluidBox(set);
	set=null;
	this.show = function(animation) {
		this.panelBox.show(animation);
	}
}