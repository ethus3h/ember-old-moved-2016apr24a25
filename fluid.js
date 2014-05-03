/* JavaScript code for Fluid//Active. */

/* TODO:

* FluidArea: 
* Row: Row of boxes in a specific order (have it take care of setting the positions relative to each other). Pass a list of Box config arrays to the constructor. This will need to understand layout element progression order of the configured language. Be able to specify widths and heighths for the inner boxes to override those computed by the automatic layout.
* Grid: Set of dynamically created Rows. Move Boxes between Rows and adjust the heighths and widths of the rows to fit nicely in a given area. Pass a list of Box config arrays to the constructor. This will need to understand layout element progression order of the configured language. Be able to specify widths and heighths for the inner boxes to override those computed by the automatic layout.
* ComputedValue: an object representing a number to be computed

*/
globalIdsCounter = 1;
function newId()
{
	globalIdsCounter++;
	return 'Box'+globalIdsCounter;
}
function getId()
{
	return 'Box'+globalIdsCounter;
}
function getAnchor(id)
{
	id = String(id).replace("Box","");
	if(id==0) {
		return "#pageContents";
	}
	return "#Box"+id;
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
	/* group is the parameter that was passed to the Box when it was instantiated */
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
//TODO: Check for constrain attachment: seems to be going to container by default instead of page contents
function Box(set) {
	AllBoxes[AllBoxes.length+1] = this;
	this.linkedBoxes = new Array();
	this.hsbox = this;
	this.cfg = set;
	console.debug(this.cfg);
	this.cfgc = clone(set);
	console.debug(this.cfgc);
	this.locked = false;
	/* HOW TO CREATE A BOX (options can be omitted if desired; default values shown):
	var set = new Object();
	set["property"] = "value";
	NewBox = new Box(set);
	set=null; */
	/* ~Explanations of parameters~
	
	~~~~~~~~~~~~~~~~~~~~~~~
	~~~~ DOCUMENTATION ~~~~
	~~~~~~~~~~~~~~~~~~~~~~~
	
	~~CONTENTS~~
	UNTESTED      contents: HTML contents of the box. Generally a <svg> tag. This will be displayed on top of the bgcolor.
	
	
	~~GENERAL LAYOUT AND GROUPING~~
	AVAILABLE     container: ID of the container box into which this box should be inserted. ID 0 is the browser window.
	AVAILABLE     autoconstrain: ID of the box that should be used for default parameters where container usually would be. ID 0 is the browser window.
	UNTESTED      zindex: stacking order of this box. Should this parameter be used? It seems it would probably be better to just have whatever box comes later in the DOM go on top (by getting a dynamically specified z-index)
	UNIMPLEMENTED crop: ID of the box to which this box should be cropped, if any. Default to 0 (the browser window) (basically that means no cropping).
	UNTESTED      input: user should be able to interact with the box (Boolean). Overrides zindex setting.

	
	~~LAYOUT~~
	UNIMPLEMENTED rotation: Rotation in degrees. Can be 0, 90, 180, or 270. Most useful set on the main container box (as opposed to enclosed boxes).
	UNIMPLEMENTED hmirroring: Whether or not to mirror the box horizontally. Boolean.
	UNIMPLEMENTED vmirroring: Whether or not to mirror the box vertically. Boolean.	
	
	
	~~SCRIPTING~~
	AVAILABLE     group: a class for the div to later be used for grouping divs
	UNTESTED      enter: A function to run when a pointer enters this box, or when a pointer is moved upon this box if it was already there when the box was show()n
	UNTESTED      leave: A function to run when a pointer exits this box
	UNTESTED      tap: A function to run when a pointer or touch input taps (clicks) this box
	
	
	~~INTERACTION~~
	UNIMPLEMENTED autoexp: Boolean. Automatically expand the box to fill autoexpatt on hover.
	UNIMPLEMENTED autoexpatt: ID of the container box this box should expand to fill on hover if autoexp is true.
	UNIMPLEMENTED autoexpbackdrop: Backdrop to draw behind this box, filling the autoexpbackdropatt box. This can be a boolean, a string (css background property), or a box config array.
	UNIMPLEMENTED autoexpbackdropatt: ID of the container box to clip the autoexpbackdrop to. ID 0 is the browser window.


	~~COMPLEX BEHAVIOR~~
	UNIMPLEMENTED behaviour: Can be box, flexrow, row, flow, grid, table, vpack, or hpack.
							 Box is default. Row, flow, grid, and pack are more complex layout systems to be used with the boxes property.
							 Flexrow is a single line of boxes, aligned based on their size.
							 Row is a single line of boxes, aligned equidistantly.
							 Flow is analogous to text in a word processing document, with boxes being reallocated to new lines when a line becomes too long for the flow layout box.
							 Grid is an equidistant grid layout (à la graph paper).
							 Table is a grid with dynamically adjusted box widths and box heights (but constant box widths within columns and constant box heighths within rows).
							 Vpack is a grid with dynamically adjusted box heighths (but constant box widths within columns).
							 Hpack is a grid with dynamically adjusted box widths (but constant box heighths within rows).
	UNIMPLEMENTED boxes: Array of Box config arrays to use to create more boxes within this one if this has a Row or Grid behavior.
	UNIMPLEMENTED blockprogression: //may be used in the future for a flexbox layout system?
	UNIMPLEMENTED lineprogression: In a flow, grid, table, vpack or hpack layout, the order in which lines should be layed out relative to each other. Can be "l", "r", "u", or "d" (left, right, up, or down).
	UNIMPLEMENTED boxprogression: In a flexrow, row, flow, grid, vpack or hpack layout, this is the direction in which the individual boxes should be layed out relative to each other. Can be "l", "r", "u", or "d" (left, right, up, or down).
	UNIMPLEMENTED boxrotation: Rotation in degrees. Can be 0, 90, 180, or 270.
	UNIMPLEMENTED justify: In a row or flow layout, how to position the contents within a line, analogous to justification of characters in a word processing document. Can be "l", "c", "f", or "r" (left, center, full, or right).

	
	~~STYLING~~
	AVAILABLE     background: Background. Can be any CSS background property
	AVAILABLE     border: Border. Can be any CSS border property
	AVAILABLE     borderstyle: Border style. Can be any CSS border-style property
	AVAILABLE     borderwidth: Border width. Can be any CSS border-width property
	AVAILABLE     bordercolor: Border color. Can be any CSS border-color property
	PARTIAL       blur: Use a blur effect on whatever's behind this box. Specify -1 for iOS 7 effect. s(This could actually create another box object below the current one with the content as the blurry SVG?) Result should be like this http://jsfiddle.net/3z6ns/ or this http://jsfiddle.net/YgHA8/1/
	UNTESTED      bluratt: element to use as the blur background
	AVAILABLE     opacity: CSS opacity value
	AVAILABLE     css: Any other arbitrary CSS to specify for this box
	
	
	~~BACKDROPS (e. g., for modals)~~
	UNIMPLEMENTED backdrop: Backdrop to draw behind this box, filling the backdropatt box. This can be a boolean, a string (css background property), or a box config array.
????UNIMPLEMENTED backdropatt: ID of the container box to clip the backdrop to. ID 0 is the browser window. [On further reflection, I think this is unnecessary since this can be specified by providing backdrop as a box config array.
	
	
	~~VERTICAL POSITIONING~~
	AVAILABLE     vpanchor: ID of the box to which this box's vertical postion should be relative. ID 0 is the browser window.
	AVAILABLE     vpattach: The vertical position at which to attach this box to the anchor box.
	UNTESTED      vpattunit: The units of vpattach. Only % or px allowed.
	UNTESTED      vptattach: The vertical position on the anchor box at which to attach this box to the anchor box.
	UNTESTED      vptattunit: The units of vptattach. Only % or px allowed.
	AVAILABLE     vpos: Vertical position of this box relative to the vpanchor box.
	AVAILABLE     vposunit: Units (%, or possibly rem?) of vpos
	UNTESTED      vconattach: The box to constrain this box's vertical size to.
	UNTESTED      vconstrain: Whether to keep the box's vertical size within the box specified in vconattach.
	UNTESTED      hanchor: ID of the box to which this box's heighth should be relative. ID 0 is the browser window.
	AVAILABLE     heighth: Width of this box relative to the hanchor box.
	AVAILABLE     hunit: Units (%, relative, or possibly rem?) of heighth. The value "relative" will set the heighth equal to the specified percentage of the computed width.
	UNTESTED      heighthpad: Additional padding to add to the heighth
	UNTESTED      heighthpadunits: Units (%, px, or rem) of heighthpad
	UNTESTED      heighthpadattach: ID of the box to which this box's heighthpad should be relative. ID 0 is the browser window.
	
	
	~~HORIZONTAL POSITIONING~~
	AVAILABLE     hpanchor: ID of the box to which this box's horizontal postion should be relative. ID 0 is the browser window.
	UNTESTED      hpattach: The horizontal position at which to attach this box to the anchor box.
	UNTESTED      hpattunit: The units of hpattach. Only % or px allowed.
	UNTESTED      hptattach: The horizontal position on the anchor box at which to attach this box to the anchor box.
	UNTESTED      hptattunit: The units of hptattach. Only % or px allowed.
	AVAILABLE     hpos: Vertical position of this box relative to the hpanchor box.
	AVAILABLE     hposunit: Units (%, or possibly rem?) of hpos
	UNTESTED      hconattach: The box to constrain this box's horizontal size to.
	UNTESTED      hconstrain: Whether to keep the box's horizontal size within the box specified in hconattach.
	UNTESTED      wanchor: ID of the box to which this box's horizontal postion should be relative. ID 0 is the browser window.
	AVAILABLE     width: Width of this box relative to the wanchor box.
	AVAILABLE     wunit: Units (%, or possibly rem?) of width
	UNTESTED      widthpad: Additional padding to add to the width
	UNTESTED      widthpadunits: Units (%, px, or rem) of widthpad
	UNTESTED      widthpadattach: ID of the box to which this box's widthpad should be relative. ID 0 is the browser window.
	
	
	~~MARGINS~~
	UNTESTED      lmar: Left margin width
	UNTESTED      rmar: Right margin width
	UNTESTED      tmar: Top margin heighth
	UNTESTED      bmar: Bottom margin heighth
	UNTESTED      lmarunit: Left margin width unit
	UNTESTED      rmarunit: Right margin width unit
	UNTESTED      tmarunit: Top margin heighth unit
	UNTESTED      bmarunit: Bottom margin heighth unit
	UNTESTED      lmarl: ID of the box to which this box's left margin width should be relative. ID 0 is the browser window.
	UNTESTED      rmarl: ID of the box to which this box's right margin width should be relative. ID 0 is the browser window.
	UNTESTED      tmarl: ID of the box to which this box's top margin heighth should be relative. ID 0 is the browser window.
	UNTESTED      bmarl: ID of the box to which this box's bottom margin heighth should be relative. ID 0 is the browser window.
	
	*/
	//Default values
	this.contents = "";
	this.boxes = undefined;
	this.behaviour = "box";
	this.background = "rgba(0,0,0,0)";
	this.backdrop = false;
	this.border = undefined;
	this.borderstyle = undefined;
	this.borderwidth = undefined;
	this.bordercolor = undefined;
	this.shadow = "";
	this.opacity = 1;
	this.blur = 0;
	this.bluratt = 10;
	this.container = 0;
	this.autoconstrain = this.container;
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
	this.widthpad = 0;
	this.widthpadunit = "%";
	this.widthpadattach = 0;
	this.hanchor = 0;
	this.heighth = 100;
	this.hunit = "%";
	this.heighthpad = 0;
	this.heighthpadunit = "%";
	this.heighthpadattach = 0;
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
	this.input = false;
	this.enter = undefined;
	this.leave = undefined;
	this.tap = undefined;
	this.css = "";
	this.bordercolor = undefined;
	//Override values if provided
	console.debug(set);
	if(typeof set["contents"] !== "undefined") { this.contents = set["contents"];}
	if(typeof set["boxes"] !== "undefined") { this.boxes = set["boxes"];}
	if(typeof set["behaviour"] !== "undefined") { this.behaviour = set["behaviour"];}
	if(typeof set["background"] !== "undefined") { this.background = set["background"];}
	if(typeof set["backdrop"] !== "undefined") { this.backdrop = set["backdrop"];}
	if(typeof set["border"] !== "undefined") { this.border = set["border"];}
	if(typeof set["borderstyle"] !== "undefined") { this.borderstyle = set["borderstyle"];}
	if(typeof set["borderwidth"] !== "undefined") { this.borderwidth = set["borderwidth"];}
	console.debug("SETBORDERWIDTH: "+set["borderwidth"]);
	console.debug("BORDERWIDTH: "+this.borderwidth);
	if(typeof set["bordercolor"] !== "undefined") { this.bordercolor = set["border"];}
	if(typeof set["shadow"] !== "undefined") { this.shadow = set["shadow"];}
	if(typeof set["opacity"] !== "undefined") { this.opacity = set["opacity"];}
	if(typeof set["blur"] !== "undefined") { this.blur = set["blur"];}
	if(typeof set["bluratt"] !== "undefined") { this.bluratt = set["bluratt"];}
	if(typeof set["container"] !== "undefined") { this.container = set["container"];}
	this.autoconstrain = this.container;
	if(typeof set["autoconstrain"] !== "undefined") { this.autoconstrain = set["autoconstrain"];}
	if(typeof set["vpanchor"] !== "undefined") { this.vpanchor = set["vpanchor"];}
	if(typeof set["vpattach"] !== "undefined") { this.vpattach = set["vpattach"];}
	if(typeof set["vpattunit"] !== "undefined") { this.vpattunit = set["vpattunit"];}
	if(typeof set["vptattach"] !== "undefined") { this.vptattach = set["vptattach"];}
	if(typeof set["vptattunit"] !== "undefined") { this.vptattunit = set["vptattunit"];}
	if(typeof set["vpos"] !== "undefined") { this.vpos = set["vpos"];}
	if(typeof set["vposunit"] !== "undefined") { this.vposunit = set["vposunit"];}
	this.vconattach = this.autoconstrain;
	if(typeof set["vconattach"] !== "undefined") { this.vconattach = set["vconattach"];}
	if(typeof set["vconstrain"] !== "undefined") { this.vconstrain = set["vconstrain"];}
	if(typeof set["hpanchor"] !== "undefined") { this.hpanchor = set["hpanchor"];}
	if(typeof set["hpattach"] !== "undefined") { this.hpattach = set["hpattach"];}
	if(typeof set["hpattunit"] !== "undefined") { this.hpattunit = set["hpattunit"];}
	if(typeof set["hptattach"] !== "undefined") { this.hpattach = set["hptattach"];}
	if(typeof set["hptattunit"] !== "undefined") { this.hpattunit = set["hptattunit"];}
	if(typeof set["hpos"] !== "undefined") { this.hpos = set["hpos"];}
	if(typeof set["hposunit"] !== "undefined") { this.hposunit = set["hposunit"];}
	this.hconattach = this.autoconstrain;
	if(typeof set["hconattach"] !== "undefined") { this.hconattach = set["hconattach"];}
	if(typeof set["hconstrain"] !== "undefined") { this.hconstrain = set["hconstrain"];}
	if(typeof set["wanchor"] !== "undefined") { this.wanchor = set["wanchor"];}
	if(typeof set["width"] !== "undefined") { this.width = set["width"];}
	if(typeof set["wunit"] !== "undefined") { this.wunit = set["wunit"];}
	if(typeof set["widthpad"] !== "undefined") { this.widthpad = set["widthpad"];}
	if(typeof set["widthpadunit"] !== "undefined") { this.widthpadunit = set["widthpadunit"];}
	if(typeof set["hanchor"] !== "undefined") { this.hanchor = set["hanchor"];}
	if(typeof set["heighth"] !== "undefined") { this.heighth = set["heighth"];}
	if(typeof set["hunit"] !== "undefined") { this.hunit = set["hunit"];}
	if(typeof set["heighthpad"] !== "undefined") { this.heighthpad = set["heighthpad"];}
	if(typeof set["heighthpadunit"] !== "undefined") { this.heighthpadunit = set["heighthpadunit"];}
	if(typeof set["crop"] !== "undefined") { this.crop = set["crop"];}
	if(typeof set["lmar"] !== "undefined") { this.lmar = set["lmar"];}
	if(typeof set["rmar"] !== "undefined") { this.rmar = set["rmar"];}
	if(typeof set["tmar"] !== "undefined") { this.tmar = set["tmar"];}
	if(typeof set["bmar"] !== "undefined") { this.bmar = set["bmar"];}
	if(typeof set["lmarunit"] !== "undefined") { this.lmarunit = set["lmarunit"];}
	if(typeof set["rmarunit"] !== "undefined") { this.rmarunit = set["rmarunit"];}
	if(typeof set["tmarunit"] !== "undefined") { this.tmarunit = set["tmarunit"];}
	if(typeof set["bmarunit"] !== "undefined") { this.bmarunit = set["bmarunit"];}
	this.lmarl = this.autoconstrain;
	this.rmarl = this.autoconstrain;
	this.tmarl = this.autoconstrain;
	this.bmarl = this.autoconstrain;
	this.widthpadattach = this.autoconstrain;
	this.heighthpadattach = this.autoconstrain;
	if(typeof set["lmarl"] !== "undefined") { this.lmarl = set["lmarl"];}
	if(typeof set["rmarl"] !== "undefined") { this.rmarl = set["rmarl"];}
	if(typeof set["tmarl"] !== "undefined") { this.tmarl = set["tmarl"];}
	if(typeof set["bmarl"] !== "undefined") { this.bmarl = set["bmarl"];}
	if(typeof set["widthpadattach"] !== "undefined") { this.widthpadattach = set["widthpadattach"];}
	if(typeof set["heighthpadattach"] !== "undefined") { this.heighthpadattach = set["heighthpadattach"];}
	if(typeof set["group"] !== "undefined") { this.group = set["group"];}
	if(typeof set["zindex"] !== "undefined") { this.zindex = set["zindex"];}
	if(typeof set["input"] !== "undefined") { this.input = set["input"];}
	if(typeof set["css"] !== "undefined") { this.css = set["css"];}
	if(typeof set["bordercolor"] !== "undefined") { this.bordercolor = set["bordercolor"];}
	if(typeof set["enter"] !== "undefined") { this.enter = set["enter"];}
	if(typeof set["leave"] !== "undefined") { this.leave = set["leave"];}
	if(typeof set["tap"] !== "undefined") { this.tap = set["tap"];}
	$(getAnchor(this.container)).append("<div id=\""+newId()+"\" class=\""+this.group+"\" style=\"display:none;"+this.css+"\">"+this.contents+"</div>");
	this.id=getId();
	this.shown = false;
	this.anchor = getAnchor(this.id);
	console.debug(this);
	$(this.anchor).css('background',this.background);
	if(typeof this.border !== "undefined") {
		$(this.anchor).css('border',this.border);
	}
	if(typeof this.borderstyle !== "undefined") {
		console.debug("Setting borderstyle to "+this.borderstyle);
		$(this.anchor).css('border-style',this.borderstyle);
	}
	if(typeof this.borderwidth !== "undefined") {
		console.debug("Setting borderwidth to "+this.borderwidth);
		$(this.anchor).css('border-width',this.borderwidth);
	}
	if(typeof this.bordercolor !== "undefined") {
		$(this.anchor).css('border-color',this.bordercolor);
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
		if(this.widthpadunit == "rem") {
			this.widthpad = this.widthpad * getRootElementEmSize();
			this.widthpadunit = "px";
		}
		if(this.heighthpadunit == "rem") {
			this.heighthpad = this.heighthpad * getRootElementEmSize();
			this.heighthpadunit = "px";
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
		heighthpadunitA = this.heighthpadunit;
		if(this.heighthpadunit == "%") {
			heighthpadunitA = "px";
		}
		widthpadunitA = this.widthpadunit;
		if(this.widthpadunit == "%") {
			widthpadunitA = "px";
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
		if(this.widthpadunit == '%') {
			this.widthpad = $(getAnchor(this.widthpadanchor)).width() * (this.widthpad / 100);
		}
		//Calculate the heighth
		computedHeighth = this.heighth+hunitA;
		if(this.hunit == '%') {
			tComputedHeighth = $(getAnchor(this.hanchor)).height() * (this.heighth / 100);
			computedHeighth = tComputedHeighth+hunitA;
		}
		if(this.heighthpadunit == '%') {
			this.heighthpad = $(getAnchor(this.heighthpadanchor)).height() * (this.heighthpad / 100);
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
				vDelta = $(getAnchor(this.vconattach)).position().top - tComputedVpa;
				if((tComputedHeighth - vDelta) > 0) {
					tComputedHeighth = tComputedHeighth - vDelta;
				}
				tComputedVpa = $(getAnchor(this.vconattach)).position().top;
			}
			if((tComputedVpa + tComputedHeighth) > ($(getAnchor(this.vconattach)).position().top + $(getAnchor(this.vconattach)).height())) {
				tComputedHeighth = $(getAnchor(this.vconattach)).height() - (tComputedVpa - $(getAnchor(this.vconattach)).position().top);
			}
		}
		if(this.hconstrain==true) {
			if(tComputedHpa < $(getAnchor(this.hconattach)).position().left) {
				hDelta = $(getAnchor(this.hconattach)).position().left - tComputedHpa;
				if((tComputedWidth - hDelta) > 0) {
					tComputedWidth = tComputedWidth - hDelta;
				}
				tComputedHpa = $(getAnchor(this.hconattach)).position().left;
			}
			if((tComputedHpa + tComputedWidth) > ($(getAnchor(this.hconattach)).position().left + $(getAnchor(this.hconattach)).width())) {
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
		computedVpa = ((tComputedVpa + parseInt(this.tmar,10))+vposunitA);
		computedHeighth = (((tComputedHeighth - parseInt(this.bmar,10)) + this.heighthpad)+hunitA);
		computedHpa = ((tComputedHpa + parseInt(this.lmar,10))+hposunitA);
		computedWidth = (((tComputedWidth - parseInt(this.rmar,10)) + this.widthpad)+wunitA);
		$(this.anchor).css('top',computedVpa);
		$(this.anchor).css('left',computedHpa);
		//console.log('Width: '+computedWidth);
		//console.log('Heighth: '+computedHeighth);
		$(this.anchor).css('height',computedHeighth);
		$(this.anchor).css('width',computedWidth);
		if(this.behaviour == "row") {
		}
		$(this.anchor).mouseover(this.enter);
		$(this.anchor).mouseout(this.leave);
		$(this.anchor).click(this.tap);
	};

	$(this.anchor).css('opacity',"0");
	$(this.anchor).css('display',"block");
	//$(this.anchor).css('z-index',"-1");
	this.compute();
	this.lock = function() {
		this.locked = true;
	}
	this.unlock = function() {
		this.locked = false;
	}
	this.toggle = function(showanimation,hideanimation){
		if(this.shown) {
			this.hide(hideanimation);
		}
		else {
			this.show(showanimation);
		}
	}
	this.stop = function() {
		if(this.hsbox !== this) {
			this.hsbox.stop();
		}
		else {
			$(this.anchor).stop(true);
		}
	}
	this.animate = function(animation,complete) {
		console.log("Animating "+this.anchor+" with animation "+animation);
		lbArrayLength = this.linkedBoxes.length;
		console.debug(this.linkedBoxes);
		for (var i = 0; i <= lbArrayLength; i++) {
			console.debug(this.linkedBoxes[i]);
			if(this.linkedBoxes[i]!=undefined) {
				this.linkedBoxes[i].animate(animation);
			}
		}
		// if(this.bg !== this) {
// 			this.hsbox.animate(animation);
// 		}
	//	else {
			if(this.locked) {
				console.log("WARNING: Not triggering animation; element locked");
			}
			else {
				this.lock();
				if(animation == "fadein") {
					$(this.anchor).css('opacity','0');
					$(this.anchor).animate({
						opacity: this.opacity,
					}, {
						duration: 250, 
						easing: "linear",
						complete: complete,
					});
				}
				if(animation == "fadeout") {
					$(this.anchor).animate({
						opacity: 0,
					}, {
						duration: 250, 
						easing: "linear",
						complete: complete,
					});
				}
				if(animation == "dropin") {
					$(this.anchor).css('opacity','0');
					$(this.anchor).css('scale', 2);
					$(this.anchor).animate({
						opacity: this.opacity,
						scale: 1
					}, {
						duration: 400, 
						easing: "linear",
						complete: complete,
					});
				}
				if(animation == "dropout") {
					$(this.anchor).animate({
						opacity: 0,
						scale: 2
					}, {
						duration: 400, 
						easing: "linear",
						complete: complete,
					});
				}
				if(animation == "zoomhalffadein") {
					$(this.anchor).css('opacity','0');
					$(this.anchor).css('display','block');
					$(this.anchor).animate({ scale: 0.5 }, 0);
					$(this.anchor).animate({
						opacity: this.opacity,
						scale: 1
					}, {
						duration: 500, 
						easing: "linear",
						complete: complete,
					});
				}
				if(animation == "zoomhalffadeout") {
					$(this.anchor).animate({
						opacity: 0,
						scale: 0.5
					}, {
						duration: 500, 
						easing: "linear",
						complete: complete,
					});
				}
				this.unlock();
		//	}
		}
	}
	this.show = function(animation){
		this.stop();
		//console.log("Showing "+this.hsanchor+" id " + this.hsbox.id + " to opacity "+this.hsbox.opacity+" with animation "+animation+"...");
		if(animation == "zoomhalffade") {
			this.animate('zoomhalffadein');
		}
		else if(animation == "fade") {
			this.animate('fadein');
		}
		else if(animation == "drop") {
			this.animate('dropin');
		}
		else {
			$(this.hsbox.anchor).css('opacity',this.opacity);
			$(this.anchor).css('opacity',this.opacity);
		}
		$(this.hsbox.anchor).css('display','block');
		$(this.anchor).css('display','block');
		this.shown = true;
	};
	this.hide = function(animation){
		this.stop();
		//console.log("Hiding "+this.hsanchor+" id " + this.hsbox.id + " with animation "+animation+"...");
		if(animation == "zoomhalffade") {
			this.animate('zoomhalffadeout');
		}
		else if(animation == "fade") {
			this.animate('fadeout');
		}
		else if(animation == "drop") {
			this.animate('dropout');
		}
		else {
			$(this.hsbox.anchor).css('opacity',0);
			$(this.anchor).css('opacity',0);
		}
		this.shown = false;
	};
	if(this.backdrop) {
		console.log("displaying backdrop");
		var set = new Object();
		set["container"] = this.container;
		set["wanchor"] = 0;
		set["group"] = 'backdroupppp';
		set["hpanchor"] = 0;
		set["vpanchor"] = 0;
		set["hanchor"] = 0;
		set["hconstrain"] = false;
		set["vconstrain"] = false;
		this.backdropContainer = new Box(set);
		set=null;
		this.linkedBoxes[this.linkedBoxes.length+1] = this.backdropContainer;
		this.backdrop = new Panel(this.backdropContainer.id,"modalbg");
		this.linkedBoxes[this.linkedBoxes.length+1] = this.backdrop;
		console.log("Backdrop: "+this.backdrop.anchor);
		console.log("Backdrop container: "+this.backdropContainer.anchor);
		console.log("This: "+this.anchor);
		
		$(this.anchor).appendTo(this.backdrop.anchor);		
	}
	if(this.blur != 0) {
		blurPtA='<svg id="';
		blurPtB='" xmlns="http://www.w3.org/2000/svg" version="1.1"><defs><filter id="';
		blurPtCa='" x="0" y="0">';
		blurPtCb='<feGaussianBlur in="SourceGraphic" stdDeviation="';
		blurPtCc=this.blur+'" />';
		if(this.blur == -1) {
			blurPtCb='<feGaussianBlur in="SourceGraphic" stdDeviation="20" />';
			blurPtCc='';
		}
		if(this.blur == -2) {
			blurPtCb='';
			blurPtCc='<feColorMatrix in="SourceGraphic" type="saturate" values="1" />';
		}
		if(this.blur == -3) {
			blurPtCb='<feComponentTransfer>\
						<feFuncR type="linear" slope="200"/>';
			blurPtCcA='<feFuncG type="linear" slope="200"/>';
			blurPtCcB='<feFuncB type="linear" slope="200"/>\
						</feComponentTransfer>';
			blurPtCc=blurPtCcA+blurPtCcB;
		}

		blurPtC=blurPtCa+blurPtCb+blurPtCc;
		blurPtD='</filter></defs></svg>';
		compiledBlur = blurPtA+newId()+blurPtB+newId()+blurPtC+blurPtD;
		blurId = getId();
		$('#pageContents').append(compiledBlur);
		var tcset = new Object();
		tcset = clone(this.cfgc);
		console.debug("DOOOOOOOD!");
		console.debug(tcset);
		tcset["blur"] = 0;
		tcset["container"] = this.container;
		tcset["backdrop"] = undefined;
		tcset["contents"] = "";
		tcset["background"] = "rgba(0,0,0,0)";
		tcset["border"] = "";
		tcset["shadow"] = "";
		tcset["css"] = "";
		tcset["group"] = "blurcontainer";
		this.blurredContainer = new Box(tcset);
		tcset=null;
		this.linkedBoxes[this.linkedBoxes.length+1] = this.blurredContainer;
		var tset = new Object();
		tset = clone(this.cfgc);
		tset["container"] = this.blurredContainer.id;
		tset["border"] = "";
		tset["backdrop"] = undefined;
		tset["shadow"] = "";
		tset["blur"] = 0;
		if(this.blur == -1) {
			tset["blur"] = -2;
		}
		if(this.blur == -2) {
			tset["blur"] = -3;
		}
		tset["wanchor"] = this.blurredContainer.id;
		tset["width"] = 100;
// 		tset["widthpad"] = this.blur;		
// 		tset["widthpadunit"] = "px";		
// 		tset["heighthpad"] = this.blur;		
// 		tset["heighthpadunit"] = "px";		
// 		if(this.blur == -1) {
// 			tset["widthpad"] = 20;
// 			tset["heighthpad"] = 20;
// 		}
// 		if(this.blur == -2) {
// 			tset["widthpad"] = 0;
// 			tset["heighthpad"] = 0;
// 		}
// 		if(this.blur == -3) {
// 			tset["widthpad"] = 0;
// 			tset["heighthpad"] = 0;
// 		}
		// tset["tmar"] = -(tset["heighthpad"] / 2);		
// 		tset["lmar"] = -(tset["widthpad"] / 2);	
// 		if(this.blur == -2) {
// 			tset["tmar"] = 0;
// 			tset["lmar"] = 0;
// 		}
// 		if(this.blur == -3) {
// 			tset["tmar"] = 0;
// 			tset["lmar"] = 0;
// 		}
// 		tset["tmarunit"] = "px";	
// 		tset["lmarunit"] = "px";	
		tset["hpanchor"] = this.blurredContainer.id;
		//tset["hpos"] = 100;
		tset["contents"] = "";
		tset["background"] = "-moz-element(#Box"+String(this.bluratt)+") no-repeat fixed";
		//tset["background"] = "rgba(255,0,255,0.5)";
		tDataA="filter:url(#";
		tDataB=blurId+");";
		tDataC=tDataA+tDataB;
		tset["css"] = tDataC;
		tset["group"] = "blurrybox";
		this.blurryBox = new Box(tset);
		tset=null;
		this.linkedBoxes[this.linkedBoxes.length+1] = this.blurryBox;
		$(this.anchor).appendTo(this.blurredContainer.anchor);
		if(typeof(this.backdropContainer) !== "undefined") {
			$(this.blurredContainer.anchor).appendTo(this.backdropContainer.anchor);			
		}
 		this.blurryBox.show("none");
 		//this.blurredContainer.show("none");
 		this.show("none");
	}
	if(typeof this.bordercolor !== "undefined") {
		$(this.anchor).css('border-color',this.bordercolor);		
	}
	this.hsanchor = this.anchor;
	if(typeof(this.blurredContainer) !== "undefined") {
		this.hsanchor = this.blurredContainer.anchor;
		this.hsbox = this.blurredContainer;
	}
	if(typeof(this.backdropContainer) !== "undefined") {
		this.hsanchor = this.backdropContainer.anchor;
		this.hsbox = this.backdropContainer;
	}
	if(this.input) {
		console.log("ZINDEX");
		$(this.anchor).css('z-index',3);
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
	this.LoadingBg = new Box(set);
	set=null;
	var set = new Object();
	set["container"] = this.LoadingBg.id;
	set["vptattach"] = 100;
	set["vpattach"] = 100;
	set["heighth"] = 0;
	set["hunit"] = "rem";
	this.Loadingremc = new Box(set);
	set=null;
	var set = new Object();
	set["container"] = this.LoadingBg.id;
	set["vpattach"] = 100;
	set["vpanchor"] = this.Loadingremc.id;
	this.LoadingConstraint = new Box(set);
	set=null;
	var set = new Object();
	set["container"] = this.LoadingBg.id;
	set["heighth"] = 75;
	this.LoadingConstraintB = new Box(set);
	set=null;
	var set = new Object();
	set["container"] = this.LoadingBg.id;
	set["vptattach"] = 50;
	set["vpos"] = 1;
	set["vposunit"] = "rem";
	set["vconattach"] = this.LoadingConstraintB.id;
	this.LoadingConstraintC = new Box(set);
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
	this.LoadingBox = new Box(set);
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
	this.LoadingCase = new Box(set);
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
	this.LoadingSpinner = new Box(set);
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
function Panel(container,style,backdrop) {
	var set = new Object();
	set["container"] = container;
	if(backdrop) {
		set["backdrop"] = true;
	}
	if(style == "white") {
		set["background"] = "rgba(255,255,255,0.7)";
		//set["border"] = "1px solid rgba(130,130,255,1)";
		set["border"] = "0.15rem solid rgba(0,179,255,1)";
		set["blur"] = 2;
	}
	else if(style == "solidwhite") {
		set["background"] = "rgba(255,255,255,1)";
		//set["border"] = "1px solid rgba(130,130,255,1)";
		set["border"] = "0.15rem solid rgba(0,179,255,1)";
	}
	else if(style == "solidwhitevert") {
		set["background"] = "rgba(255,255,255,1)";
		set["borderstyle"] = "solid";
		set["borderwidth"] = "0rem 0.15rem 0rem 0.15rem";
		set["bordercolor"] =  "rgba(0,179,255,1)";
		set["shadow"] = "0rem 0rem 3rem #FFFFFF";
	}
	else if(style == "modalbg") {
		set["background"] = "rgba(0,0,0,0.8)";
		set["hpattach"] = 0;
		set["vpattach"] = 0;
		set["blur"] = 1.5;
	}
	else if(style == "ios") {
	    //This still doesn't work right. :(
		set["background"] = "rgba(255,255,255,0.1)";
		set["blur"] = -1;
	}
	else {
		set["background"] = "rgba(206,190,232,0.4)";
		set["border"] = "0.1rem solid rgba(255,255,255,0.75)";
		set["shadow"] = "0rem 0rem 0.2rem #FFFFFF";
		set["blur"] = 2;
	}
	set["vconattach"] = container;
	set["hconattach"] = container;
	this.panelBox = new Box(set);
	console.debug(set);
	console.debug("NEW PANEL CREATED: "+this.panelBox.anchor);
	this.id = this.panelBox.id;
	this.anchor = this.panelBox.anchor;
	set=null;
	this.show = function(animation) {
		this.panelBox.show(animation);
	} 
	this.hide = function(animation) {
		this.panelBox.hide(animation);
	} 
	this.animate = function(animation) {
		this.panelBox.animate(animation);
	} 
	this.toggle = function(animation) {
		this.panelBox.toggle(animation);
	} 
	this.compute = function() {
		this.panelBox.compute();
	} 
}