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

function BoxCfg(contents,background,opacity,blur,container,vpanchor,vpattach,vpattunit,vpos,vposunit,vconattach,vconstrain,hpanchor,hpattach,hpattunit,
hpos,hposunit,hconattach,hconstrain,wanchor,width,wunit,hanchor,heighth,hunit,crop,group,zindex,css) {
	this.contents = contents;
	this.background = background;
	this.opacity = opacity;
	this.blur = blur;
	this.container = container;
	this.vpanchor = vpanchor;
	this.vpattach = vpattach;
	this.vpattunit = vpattunit;
	this.vpos = vpos;
	this.vposunit = vposunit;
	this.vconattach = vconattach;
	this.vconstrain = vconstrain;
	this.hpanchor = hpanchor;
	this.hpattach = hpattach;
	this.hpattunit = hpattunit;
	this.hpos = hpos;
	this.hposunit = hposunit;
	this.hconattach = hconattach;
	this.hconstrain = hconstrain;
	this.wanchor = wanchor;
	this.width = width;
	this.wunit = wunit;
	this.hanchor = hanchor;
	this.heighth = heighth;
	this.hunit = hunit;
	this.crop = crop;
	this.group = group;
	this.zindex = zindex;
	this.css = css;
}

//TODO: Constrained boxes currently don't work.
//TODO: blur, crop, and zindex are unimplemented.
function FluidBox() {
	var args = Args([
		{contents: Args.STRING | Args.Optional,
			_default: ""},
		{background: Args.STRING    | Args.Optional,
			_default: "rgba(0,0,0,0)"},
		{opacity: Args.INT    | Args.Optional,
			_default: 1},
		{blur: Args.INT    | Args.Optional,
			_default: 0},
		{container: Args.INT    | Args.Optional,
			_default: 0},
		{vpanchor: Args.INT    | Args.Optional,
			_default: 0},
		{vpattach: Args.INT    | Args.Optional,
			_default: 0},
		{vpattunit: Args.STRING    | Args.Optional,
			_default: "%"},
		{vpos: Args.INT    | Args.Optional,
			_default: 0},
		{vposunit: Args.STRING    | Args.Optional,
			_default: "%"},
		{vconattach: Args.INT    | Args.Optional,
			_default: 0},
		{vconstrain: Args.BOOL    | Args.Optional,
			_default: true},
		{hpanchor: Args.INT    | Args.Optional,
			_default: 0},
		{hpattach: Args.INT    | Args.Optional,
			_default: 0},
		{hpattunit: Args.STRING    | Args.Optional,
			_default: "%"},
		{hpos: Args.INT    | Args.Optional,
			_default: 0},
		{hposunit: Args.STRING    | Args.Optional,
			_default: "%"},
		{hconattach: Args.INT    | Args.Optional,
			_default: 0},
		{hconstrain: Args.BOOL    | Args.Optional,
			_default: true},
		{wanchor: Args.INT    | Args.Optional,
			_default: 0},
		{width: Args.INT    | Args.Optional,
			_default: 100},
		{wunit: Args.STRING    | Args.Optional,
			_default: "%"},
		{hanchor: Args.INT    | Args.Optional,
			_default: 0},
		{heighth: Args.INT    | Args.Optional,
			_default: 100},
		{hunit: Args.STRING    | Args.Optional,
			_default: "%"},
		{crop: Args.INT    | Args.Optional,
			_default: 0},
		{group: Args.STRING    | Args.Optional,
			_default: ""},
		{zindex: Args.INT    | Args.Optional,
			_default: 0},
		{css: Args.STRING    | Args.Optional,
			_default: ""},
				], arguments);

/* Usage:
	exampleBox = new FluidBox({contents: "", background: "rgba(0,0,0,0)", opacity: 1, blur: 0,
		container: 0, vpanchor: 0, vpattach: 0, vpattunit: "%", vpos: 0, vposunit: "%",
		vconattach: 0, vconstrain: true, hpanchor: 0, hpattach: 0, hpattunit: "%",
		hpos: 0, hposunit: "%", hconattach: 0, hconstrain: true, wanchor: 0, width: 100,
		wunit: "%", hanchor: 0, heighth: 100, hunit: "%", crop: 0, group: "", zindex: 0,
		css: ""});
		*/
	AllBoxes[AllBoxes.length+1] = this;
	//console.debug(AllBoxes);
	/* ~Explanations of parameters~
	contents: HTML contents of the box. Generally a <svg> tag. This will be displayed on top of the bgcolor.
	background: Background. Can be any CSS background
	opacity: CSS opacity value
	blur: Use a blur effect on whatever's behind this box. (This could actually create another box object below the current one with the content as the blurry SVG?) Result should be like this http://jsfiddle.net/3z6ns/ or this http://jsfiddle.net/YgHA8/1/
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
	/* this.contents = contents;
	this.background = background;
	this.opacity = opacity;
	this.blur = blur;
	this.container = container;
	this.vpanchor = vpanchor;
	this.vpattach = vpattach;
	this.vpattunit = vpattunit;
	this.vpos = vpos;
	//console.log("Sent vpos: "+vpos);
	//console.log("New vpos: "+this.vpos);
	this.vposunit = vposunit;
	this.vconattach = vconattach;
	this.vconstrain = vconstrain;
	this.hpanchor = hpanchor;
	this.hpattach = hpattach;
	this.hpattunit = hpattunit;
	this.hpos = hpos;
	//console.log("Sent hpos: "+hpos);
	//console.log("New hpos: "+this.hpos);
	this.hposunit = hposunit;
	this.hconattach = hconattach;
	this.hconstrain = hconstrain;
	this.wanchor = wanchor;
	this.width = width;
	this.wunit = wunit;
	this.hanchor = hanchor;
	this.heighth = heighth;
	this.hunit = hunit;
	this.crop = crop;
	this.group = group;
	this.zindex = zindex;
	this.css = css; */
	console.log("Box instantiating. Contents = "+this.contents+", background = "+this.background
	+", opacity = "+this.opacity+", blur = "+this.blur+", container = "+this.container+", vpanchor = "+this.vpanchor+", vpattach = "+this.vpattach+", vpattunit = "+this.vpattunit+", vpos = "+this.vpos
	+", vposunit = "+this.vposunit+", vconattach = "+this.vconattach+", vconstrain = "+this.vconstrain+", hpanchor = "+this.hpanchor+", hpattach = "+this.hpattach+", hpattunit = "+this.hpattunit+", hpos = "+this.hpos
	+", hposunit = "+this.hposunit+", hconattach = "+this.hconattach+", hconstrain = "+this.hconstrain+", wanchor = "+this.wanchor+", width = "+this.width
	+", wunit = "+this.wunit+", hanchor = "+this.hanchor+", heighth = "+this.heighth
	+", hunit = "+this.hunit+", crop = "+this.crop+", group = "+this.group+", zindex = "+this.zindex+", css = "+this.css);
	/* //console.log(getAnchor(this.vpanchor)); */
	/* $(getAnchor(this.vpanchor)).append("<div id=\""+newId()+"\" style=\"background:"+this.background+";height:"+this.heighth+this.hunit";width:"+this.width+this.wunit+";position:relative;left:"+this.hpos+";top:"+this.vpos+"\">"+"</div>"); */
	$(getAnchor(this.container)).append("<div id=\""+newId()+"\" class=\""+this.group+"\" style=\"display:none;"+this.css+"\">"+this.contents+"</div>");
	this.id=getId();
	this.anchor = getAnchor(this.id);
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
		if(this.vposunit == '%') {
			//console.log("vpanchor "+getAnchor(this.vpanchor)+" top: "+$(getAnchor(this.vpanchor)).position().top);
			//console.log("vpanchor heighth: " + $(getAnchor(this.vpanchor)).height());
			tComputedVpos = ($(getAnchor(this.vpanchor)).position().top + ($(getAnchor(this.vpanchor)).height() * (this.vpos / 100)));
			computedVpos = tComputedVpos+'px';
		}
		//Calculate the hpos
		computedHpos = this.hpos+this.hposunit;
		if(this.hposunit == '%') {
			//console.log("hpanchor left: "+$(getAnchor(this.hpanchor)).position().left);
			//console.log("hpanchor width: "+$(getAnchor(this.hpanchor)).width());
			tComputedHpos = ($(getAnchor(this.hpanchor)).position().left + ($(getAnchor(this.hpanchor)).width() * (this.hpos / 100)));
			computedHpos = tComputedHpos+'px';
		}
		$(this.anchor).css('height',computedHeighth);
		$(this.anchor).css('width',computedWidth);
		//Calculate the vertical attach point
		computedVpa = this.vpattach;
		if(this.vpattunit == '%') {
			tComputedVpa = $(this.anchor).height() * (this.vpattach / 100);
			computedVpa = (tComputedVpos - tComputedVpa)+"px";
		}
		//Calculate the horizontal attach point
		computedHpa = this.hpattach;
		if(this.hpattunit == '%') {
			tComputedHpa = $(this.anchor).width() * (this.hpattach / 100);
			computedHpa = (tComputedHpos - tComputedHpa)+"px";
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
			console.log("vconstrain enabled");
			if(tComputedVpa < $(getAnchor(this.vconattach)).position().top) {
				console.log("Upper constraint condition failed: "+tComputedVpa+" is less than "+$(getAnchor(this.vconattach)).position().top);
				tComputedVpa = $(getAnchor(this.vconattach)).position().top;
				console.log("Setting vpos of "+this.anchor+" to "+tComputedVpa);
			}
			if((tComputedVpa + tComputedHeighth) > ($(getAnchor(this.vconattach)).position().top + $(getAnchor(this.vconattach)).height)) {
				console.log("Lower constraint condition failed: "+(tComputedVpa + tComputedHeighth)+" is greater than "+($(getAnchor(this.vconattach)).position().top + $(getAnchor(this.vconattach)).height));
				tComputedHeighth = $(getAnchor(this.vconattach)).height - (tComputedVpa - $(getAnchor(this.vconattach)).position().top);
				console.log("Setting heighth of "+this.anchor+" to "+tComputedHeighth);
			}
		}
		if(this.hconstrain==true) {
			console.log("hconstrain enabled");
			if(tComputedHpa < $(getAnchor(this.hconattach)).position().left) {
				console.log("Left constraint condition failed: "+tComputedHpa+" is less than "+$(getAnchor(this.hconattach)).position().left);
				tComputedHpa = $(getAnchor(this.hconattach)).position().left;
				console.log("Setting hpos of "+this.anchor+" to "+tComputedHpa);
			}
			if((tComputedHpa + tComputedWidth) > ($(getAnchor(this.hconattach)).position().left + $(getAnchor(this.hconattach)).width)) {
				console.log("Right constraint condition failed: "+(tComputedHpa + tComputedWidth)+" is greater than "+($(getAnchor(this.hconattach)).position().width + $(getAnchor(this.hconattach)).width));
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
	//var LoadingBg = new FluidBox("","#b7b0b0",1,0,0,0,0,"%",0,"%",null,false,0,0,"%",0,"%",0,true,0,100,"%",0,100,"%",null,null,null,null);
	LoadingBg = new FluidBox({contents: "", background: "#b7b0b0", opacity: 1, blur: 0,
		container: 0, vpanchor: 0, vpattach: 0, vpattunit: "%", vpos: 0, vposunit: "%",
		vconattach: 0, vconstrain: true, hpanchor: 0, hpattach: 0, hpattunit: "%",
		hpos: 0, hposunit: "%", hconattach: 0, hconstrain: true, wanchor: 0, width: 100,
		wunit: "%", hanchor: 0, heighth: 100, hunit: "%", crop: 0, group: null, zindex: null,
		css: ""});
	////console.log("LoadingBg id = "+LoadingBg.anchor);
	var LoadingBox = new FluidBox("Loading…", "rgba(0,0,0,0)",1,0,LoadingBg.id,LoadingBg.id,50,"%",25,"%",0,true,LoadingBg.id,50,"%",50,"%",null,false,0,10,"rem",0,3,"rem",null,"loadingMessageContainer",null,"font-size:3rem;font-family:'Lato',sans-serif;color:#444444;display:flex;align-items:center;flex-flow:column");
	////console.log("LoadingBox id = "+LoadingBox.anchor);
	var LoadingSpinner = new FluidBox("", "rgba(0,0,0,0)",1,0,LoadingBox.id,LoadingBox.id,0,"%",135,"%",0,true,LoadingBox.id,50,"%",50,"%",null,false,LoadingBox.id,75,"%",LoadingBox.id,100,"relative",null,"loadingSpinnerContainer",null,"margin-left:auto;margin-right:auto;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;-ms-box-sizing:border-box;box-sizing:border-box;margin:auto;border-width:0.1rem;border-style:solid;border-color:#444444 transparent transparent;border-radius:50%;-webkit-animation:spin 2.2s linear infinite;animation:spin 2.2s linear infinite");
	////console.log("LoadingSpinner id = "+LoadingSpinner.anchor);
	LoadingSpinner.show("none"); 
	LoadingBox.show("none"); 
	LoadingBg.show("fade"); 
}
LoadingScreen(0);
