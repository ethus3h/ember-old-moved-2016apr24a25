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
	//console.log("ID " + id + " requested");
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
	//console.log("Recomputing metrics");
	//console.debug(AllBoxes);
	for (var i = 1; i < AllBoxes.length; i = i + 1 ) {
		if(AllBoxes[i]!=undefined) {
			//console.log("Recomputing metrics for: ");
			//console.debug(AllBoxes[i]);
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
	set["contents"] = "";
	set["background"] = "rgba(0,0,0,0)";
	set["opacity"] = 1;
	set["blur"] = 0;
	set["container"] = 0;
	set["vpanchor"] = 0;
	set["vpattach"] = 0;
	set["vpattunit"] = "%";
	set["vpos"] = 0;
	set["vposunit"] = "%";
	set["vconattach"] = 0;
	set["vconstrain"] = true;
	set["hpanchor"] = 0;
	set["hpattach"] = 0;
	set["hpattunit"] = "%";
	set["hpos"] = 0;
	set["hposunit"] = "%";
	set["hconattach"] = 0;
	set["hconstrain"] = true;
	set["wanchor"] = 0;
	set["width"] = 100;
	set["wunit"] = "%";
	set["hanchor"] = 0;
	set["heighth"] = 100;
	set["hunit"] = "%";
	set["crop"] = 0;
	set["group"] = "";
	set["zindex"] = undefined;
	set["css"] = "";
	NewFluidBox = new FluidBox(set); */
	
	//console.debug(AllBoxes);
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
	/* //console.log(getAnchor(this.vpanchor)); */
	/* $(getAnchor(this.vpanchor)).append("<div id=\""+newId()+"\" style=\"background:"+this.background+";height:"+this.heighth+this.hunit";width:"+this.width+this.wunit+";position:relative;left:"+this.hpos+";top:"+this.vpos+"\">"+"</div>"); */
	$(getAnchor(this.container)).append("<div id=\""+newId()+"\" class=\""+this.group+"\" style=\"display:none;"+this.css+"\">"+this.contents+"</div>");
	this.id=getId();
	this.anchor = getAnchor(this.id);
	console.log("Box " + this.anchor + " instantiating. Contents = "+this.contents+", background = "+this.background
	+", opacity = "+this.opacity+", blur = "+this.blur+", container = "+this.container+", vpanchor = "+this.vpanchor+", vpattach = "+this.vpattach+", vpattunit = "+this.vpattunit+", vpos = "+this.vpos
	+", vposunit = "+this.vposunit+", hpanchor = "+this.hpanchor+", hpattach = "+this.hpattach+", hpattunit = "+this.hpattunit+", hpos = "+this.hpos
	+", hposunit = "+this.hposunit+", wanchor = "+this.wanchor+", width = "+this.width
	+", wunit = "+this.wunit+", hanchor = "+this.hanchor+", heighth = "+this.heighth
	+", hunit = "+this.hunit+", crop = "+this.crop+", group = "+this.group+", zindex = "+this.zindex+", css = "+this.css);
	$(this.anchor).css('background',this.background);
	$(this.anchor).css('opacity',this.opacity);
	$(this.anchor).css('background-size','cover');
	$(this.anchor).css('position','fixed');
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
		console.log("computedVpos: "+computedVpos);
		if(this.vposunit == '%') {
			tComputedVpos = ($(getAnchor(this.vpanchor)).position().top + ($(getAnchor(this.vpanchor)).height() * (this.vpos / 100)));
			computedVpos = tComputedVpos+'px';
		}
		//Calculate the hpos
		computedHpos = this.hpos+this.hposunit;
		console.log("computedHpos: "+computedHpos);
		if(this.hposunit == '%') {
			tComputedHpos = ($(getAnchor(this.hpanchor)).position().left + ($(getAnchor(this.hpanchor)).width() * (this.hpos / 100)));
			computedHpos = tComputedHpos+'px';
		}
		$(this.anchor).css('height',computedHeighth);
		$(this.anchor).css('width',computedWidth);
		//Calculate the vertical attach point
		computedVpa = this.vpattach;
		if(this.vpattunit == '%') {
			tComputedVpa = tComputedVpos - ($(this.anchor).height() * (this.vpattach / 100));
			computedVpa = tComputedVpa+"px";
			console.log("computedVpa: "+computedVpa);
		}
		//Calculate the horizontal attach point
		computedHpa = this.hpattach;
		if(this.hpattunit == '%') {
			tComputedHpa = tComputedHpos - ($(this.anchor).width() * (this.hpattach / 100));
			computedHpa = tComputedHpa+"px";
			console.log("computedHpa: "+computedHpa);
		}
		//TODO: Actually get this working
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
				tComputedVpa = $(getAnchor(this.vconattach)).position().top;
				console.log("Setting vpos of "+this.anchor+" to "+tComputedVpa);
			}
			if((tComputedVpa + tComputedHeighth) > ($(getAnchor(this.vconattach)).position().top + $(getAnchor(this.vconattach)).height)) {
				tComputedHeighth = $(getAnchor(this.vconattach)).height - (tComputedVpa - $(getAnchor(this.vconattach)).position().top);
				console.log("Setting heighth of "+this.anchor+" to "+tComputedHeighth);
			}
		}
		if(this.hconstrain==true) {
			if(tComputedHpa < $(getAnchor(this.hconattach)).position().left) {
				tComputedHpa = $(getAnchor(this.hconattach)).position().left;
				console.log("Setting hpos of "+this.anchor+" to "+tComputedHpa);
			}
			if((tComputedHpa + tComputedWidth) > ($(getAnchor(this.hconattach)).position().left + $(getAnchor(this.hconattach)).width)) {
				tComputedWidth = $(getAnchor(this.hconattach)).width - (tComputedHpa - $(getAnchor(this.hconattach)).position().left);
				console.log("Setting width of "+this.anchor+" to "+tComputedWidth);
			}
		}
		computedVpa = tComputedVpa+"px";
		computedHeighth = tComputedHeighth+"px";
		computedHpa = tComputedHpa+"px";
		computedWidth = tComputedWidth+"px";
		
		//Previous implementation
		/*
		if(this.vconstrain == true) {
			if((tComputedVpa + tComputedHeighth) > ($(getAnchor(this.vconattach)).position().top) + $(getAnchor(this.hconattach)).height) {
				tComputedHeighth = $(getAnchor(this.hconattach)).height;
				computedHeighth = tComputedHeighth+'px';
				if((tComputedVpa < $(getAnchor(this.vconattach)).position().top) | (tComputedVpa > ($(getAnchor(this.vconattach)).position().top + $(getAnchor(this.vconattach)).heigth()))) {
					tComputedVpa = $(getAnchor(this.vconattach)).position().top;
				}
				computedVpa = (tComputedVpos - tComputedVpa)+"px";
				$(this.anchor).css('height',computedHeighth);
			}
		}
		if(this.hconstrain == true) {
			//console.debug(getAnchor(this.hconattach));
			if((tComputedHpa + tComputedWidth) > ($(getAnchor(this.hconattach)).position().left) + $(getAnchor(this.hconattach)).width) {
				tComputedWidth = $(getAnchor(this.hconattach)).width;
				computedWidth = tComputedWidth+'px';
				if((tComputedHpa < $(getAnchor(this.hconattach)).position().left) | (tComputedHpa > ($(getAnchor(this.hconattach)).position().left + $(getAnchor(this.hconattach)).width()))) {
					tComputedHpa = $(getAnchor(this.hconattach)).position().left;
				}
				computedHpa = (tComputedHpos - tComputedHpa)+"px";
				$(this.anchor).css('width',computedWidth);
			}
		}*/
		$(this.anchor).css('top',computedVpa);
		$(this.anchor).css('left',computedHpa);
		$(this.anchor).css('height',computedHeighth);
		$(this.anchor).css('width',computedWidth);
		//console.log("Set vpos: "+this.vpos);
		//console.log("Partial vpos: "+tComputedVpos);
		//console.log("Computed vpos: "+computedVpos);
		//console.log("Set hpos: "+this.hpos);
		//console.log("Partial hpos: "+tComputedHpos);
		//console.log("Computed hpos: "+computedHpos);
		//console.log("Set vpa: "+this.vpattach);
		//console.log("Partial vpa: "+tComputedVpa);
		//console.log("Computed vpa: "+computedVpa);
		//console.log("Set hpa: "+this.hpattach);
		//console.log("Partial hpa: "+tComputedHpa);
		//console.log("Computed hpa: "+computedHpa);
	};
	$(this.anchor).css('opacity',"0");
	$(this.anchor).css('display',"block");
	this.compute();
	this.show = function(animation){
		////console.log("Displaying box "+this.id+"; ID #"+getId());
		targetElement = this.anchor;
		//console.debug($(this.anchor));
		if(animation == "none") {
			////console.log("Animation: none");
			//console.debug($(this.anchor));
			$(targetElement).css('display','block');
			$(targetElement).css('opacity',this.opacity);
			//console.debug($(this.anchor));
		}
		if(animation == "zoom") {
			////console.log("Animation: zoom");
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
			//console.debug($(this.anchor));
			//$(targetElement).css('display','block');
			//console.debug($(this.anchor));
		}
		if(animation == "fade") {
			////console.log("Animation: fade");
			

			$(targetElement).css('opacity','0');
			$(targetElement).show();
			$(targetElement).animate({
				opacity: this.opacity,
			}, 250, "linear");
			//console.debug($(this.anchor));
			//$(targetElement).css('display','block');
			//console.debug($(this.anchor));
		}
		//console.debug($(this.anchor));
		//$(targetElement).css('display','block');
		//$(targetElement).show();
		//console.debug($(this.anchor));
		////console.log(targetElement);

	};
}
function LoadingScreen(container) {
	var set = new Object();
	set["background"] = "#b7b0b0";
	LoadingBg = new FluidBox(set);
	var set = new Object();
	set["contents"] = "Loading...";
	set["container"] = LoadingBg.id;
	set["vpanchor"] = LoadingBg.id;
	set["vpos"] = 50;
	set["hpanchor"] = LoadingBg.id;
	set["hpos"] = 50;
	LoadingBox = new FluidBox(set);
	////console.log("LoadingBg id = "+LoadingBg.anchor);
	//var LoadingBox = new FluidBox("Loading…", "rgba(0,0,0,0)",1,0,LoadingBg.id,LoadingBg.id,50,"%",25,"%",0,true,LoadingBg.id,50,"%",50,"%",null,false,0,10,"rem",0,3,"rem",null,"loadingMessageContainer",null,"font-size:3rem;font-family:'Lato',sans-serif;color:#444444;display:flex;align-items:center;flex-flow:column");
	////console.log("LoadingBox id = "+LoadingBox.anchor);
	//var LoadingSpinner = new FluidBox("", "rgba(0,0,0,0)",1,0,LoadingBox.id,LoadingBox.id,0,"%",135,"%",0,true,LoadingBox.id,50,"%",50,"%",null,false,LoadingBox.id,75,"%",LoadingBox.id,100,"relative",null,"loadingSpinnerContainer",null,"margin-left:auto;margin-right:auto;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;-ms-box-sizing:border-box;box-sizing:border-box;display:block;width:100%;height:100%;margin:auto;border-width:0.1rem;border-style:solid;border-color:#444444 transparent transparent;border-radius:50%;-webkit-animation:spin 2.2s linear infinite;animation:spin 2.2s linear infinite");
	////console.log("LoadingSpinner id = "+LoadingSpinner.anchor);
	//LoadingSpinner.show("none"); 
	//LoadingBox.show("none"); 
	LoadingBg.show("fade"); 
}
LoadingScreen(0);