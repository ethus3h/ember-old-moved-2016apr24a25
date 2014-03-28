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
	console.log("ID " + id + " requested");
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
	console.log("Recomputing metrics");
	console.debug(AllBoxes);
	for (var i = 1; i < AllBoxes.length; i = i + 1 ) {
		if(AllBoxes[i]!=undefined) {
			console.log("Recomputing metrics for: ");
			console.debug(AllBoxes[i]);
			AllBoxes[i].compute();
		}
	}
};



function FluidBox(contents,background,blur,container,vpanchor,vpattach,vpattunit,vpos,vposunit,vconattach,vconstrain,hpanchor,hpattach,hpattunit,
hpos,hposunit,hconattach,hconstrain,wanchor,width,wunit,hanchor,heighth,hunit,crop,group,zindex) {
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
	this.vconattach = vconattach;
	this.vconstrain = vconstrain;
	this.hpanchor = hpanchor;
	this.hpattach = hpattach;
	this.hpattunit = hpattunit;
	this.hpos = hpos;
	console.log("Sent hpos: "+hpos);
	console.log("New hpos: "+this.hpos);
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
	$(this.anchor).css('background',this.background);
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
			console.log("vpanchor top: "+$(getAnchor(this.vpanchor)).position().top);
			console.log("vpanchor heighth: " + $(getAnchor(this.vpanchor)).height());
			tComputedVpos = ($(getAnchor(this.vpanchor)).position().top + ($(getAnchor(this.vpanchor)).height() * (this.vpos / 100)));
			computedVpos = tComputedVpos+'px';
		}
		//Calculate the hpos
		computedHpos = this.hpos+this.hposunit;
		if(this.hposunit == '%') {
			console.log("hpanchor left: "+$(getAnchor(this.hpanchor)).position().left);
			console.log("hpanchor width: "+$(getAnchor(this.hpanchor)).width());
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
		if(this.vconstrain == true) {
			if((tComputedVpa + tComputedHeighth) > ($(this.vconattach).position().top) + $(this.hconattach).height) {
				tComputedHeighth = $(this.hconattach).height;
				computedHeighth = tComputedHeighth+'px';
				if((tComputedVpa < $(this.vconattach).position().top) | (tComputedVpa > ($(this.vconattach).position().top + $(this.vconattach).heigth()))) {
					tComputedVpa = $(this.vconattach).position().top;
				}
				computedVpa = (tComputedVpos - tComputedVpa)+"px";
				$(this.anchor).css('height',computedHeighth);
			}
		}
		if(this.hconstrain == true) {
			if((tComputedHpa + tComputedWidth) > ($(this.hconattach).position().left) + $(this.hconattach).width) {
				tComputedWidth = $(this.hconattach).width;
				computedWidth = tComputedWidth+'px';
				if((tComputedHpa < $(this.hconattach).position().left) | (tComputedHpa > ($(this.hconattach).position().left + $(this.hconattach).width()))) {
					tComputedHpa = $(this.hconattach).position().left;
				}
				computedHpa = (tComputedHpos - tComputedHpa)+"px";
				$(this.anchor).css('width',computedWidth);
			}
		}
		$(this.anchor).css('top',computedVpa);
		$(this.anchor).css('left',computedHpa);
		console.log("Set vpos: "+this.vpos);
		console.log("Partial vpos: "+tComputedVpos);
		console.log("Computed vpos: "+computedVpos);
		console.log("Set hpos: "+this.hpos);
		console.log("Partial hpos: "+tComputedHpos);
		console.log("Computed hpos: "+computedHpos);
		console.log("Set vpa: "+this.vpattach);
		console.log("Partial vpa: "+tComputedVpa);
		console.log("Computed vpa: "+computedVpa);
		console.log("Set hpa: "+this.hpattach);
		console.log("Partial hpa: "+tComputedHpa);
		console.log("Computed hpa: "+computedHpa);
	};
	this.compute();
	this.show = function(animation){
		//console.log("Displaying box "+this.id+"; ID #"+getId());
		targetElement = this.anchor;
		//console.debug($(this.anchor));
		if(animation == "none") {
			//console.log("Animation: none");
			//console.debug($(this.anchor));
			$(targetElement).css('display','block');
			//console.debug($(this.anchor));
		}
		if(animation == "zoom") {
			//console.log("Animation: zoom");
			$(targetElement).css('top',(tComputedHeighth/4)+'px');
			$(targetElement).css('left',(tComputedWidth/4)+'px');
			$(targetElement).css('width',(tComputedWidth/2)+'px');
			$(targetElement).css('height',(tComputedHeighth/2)+'px');
			$(targetElement).css('opacity','0');
			$(targetElement).show();
			bodyWidth = $('body').width();
			bodyHeighth = $('body').height();
			$(targetElement).animate({
				opacity: 1,
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
			//console.log("Animation: fade");
			

			$(targetElement).css('opacity','0');
			$(targetElement).show();
			$(targetElement).animate({
				opacity: 1,
			}, 250, "linear");
			//console.debug($(this.anchor));
			//$(targetElement).css('display','block');
			//console.debug($(this.anchor));
		}
		//console.debug($(this.anchor));
		//$(targetElement).css('display','block');
		//$(targetElement).show();
		//console.debug($(this.anchor));
		//console.log(targetElement);

	};
}
function LoadingScreen(container) {
	var LoadingBg = new FluidBox("","#b7b0b0",0,0,0,0,"%",0,"%",null,false,0,0,"%",0,"%",null,false,0,100,"%",0,100,"%",null,null,null);
	console.log("LoadingBg id = "+LoadingBg.anchor);
	var LoadingBox = new FluidBox("", "rgba(255,0,0,1)",0,LoadingBg.id,LoadingBg.id,50,"%",50,"%",null,false,LoadingBg.id,50,"%",50,"%",null,false,0,10,"%",0,110,"relative",null,null,null);
	console.log("LoadingBox id = "+LoadingBox.anchor);
	var LoadingSpinner = new FluidBox("", "rgba(255,255,0,1)",0,LoadingBox.id,LoadingBox.id,0,"%",110,"%",null,false,LoadingBox.id,0,"%",100,"%",null,false,LoadingBox.id,10,"%",LoadingBox.id,100,"relative",null,null,null);
	console.log("LoadingSpinner id = "+LoadingSpinner.anchor);
	LoadingSpinner.show("none"); 
	LoadingBox.show("none"); 
	LoadingBg.show("fade"); 
}
LoadingScreen(0);
