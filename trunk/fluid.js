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

		

        function FluidBox(contents,background,blur,container,vpanchor,vpos,vposunit,hpanchor,
        hpos,hposunit,wanchor,width,wunit,hanchor,heighth,hunit,crop,group,zindex) {
        	AllBoxes[AllBoxes.length+1] = this;
        	console.debug(AllBoxes);
        	/* ~Explanations of parameters~
        	contents: HTML contents of the box. Should be the contents of a <svg> tag. This will be displayed on top of the bgcolor.
        	background: Background. Can be any CSS background
        	blur: Use a blur effect on whatever's behind this box. (This could actually create another box object below the current one with the content as the blurry SVG?) Result should be like this http://jsfiddle.net/3z6ns/ or this http://jsfiddle.net/YgHA8/1/
        	container: ID of the container box into which this box should be inserted. ID 0 is the browser window.
			vpanchor: ID of the box to which this box's vertical postion should be relative. ID 0 is the browser window.
			vpos: Vertical position of this box relative to the vpanchor box.
			vposunit: Units (%, or possibly rem?) of vpos
			hpanchor: ID of the box to which this box's horizontal postion should be relative. ID 0 is the browser window.
			hpos: Vertical position of this box relative to the hpanchor box.
			hposunit: Units (%, or possibly rem?) of hpos
			wanchor: ID of the box to which this box's horizontal postion should be relative. ID 0 is the browser window.
			width: Width of this box relative to the wanchor box.
			wunit: Units (%, or possibly rem?) of width
			hanchor: ID of the box to which this box's heighth should be relative. ID 0 is the browser window.
			heighth: Width of this box relative to the hanchor box.
			hunit: Units (%, or possibly rem?) of heighth
			crop: ID of the box to which this box should be cropped, if any. Default to 0 (the browser window) (basically that means no cropping).
			group: a class for the div to later be used for grouping divs
			zindex: stacking order of this box. Should this parameter be used? It seems it would probably be better to just have whatever box comes later in the DOM go on top (by getting a dynamically specified z-index)
			*/
			this.contents = contents;
			this.background = background;
			this.blur = blur;
			this.container = container;
			this.vpanchor = vpanchor;
			this.vpos = vpos;
			this.vposunit = vposunit;
			this.hpanchor = hpanchor;
			this.hpos = hpos;
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
			+", blur = "+this.blur+", container = "+this.container+", vpanchor = "+this.vpanchor+", vpos = "+this.vpos
			+", vposunit = "+this.vposunit+", hpanchor = "+this.hpanchor+", hpos = "+this.hpos
			+", hposunit = "+this.hposunit+", wanchor = "+this.wanchor+", width = "+this.width
			+", wunit = "+this.wunit+", hanchor = "+this.hanchor+", heighth = "+this.heighth
			+", hunit = "+this.hunit+", crop = "+this.crop+", group = "+this.group+", zindex = "+this.zindex);
			/* console.log(getAnchor(this.vpanchor)); */
			/* $(getAnchor(this.vpanchor)).append("<div id=\""+newId()+"\" style=\"background:"+this.background+";height:"+this.heighth+this.hunit";width:"+this.width+this.wunit+";position:relative;left:"+this.hpos+";top:"+this.vpos+"\">"+"</div>"); */
			$(getAnchor(this.container)).append("<div id=\""+newId()+"\" class=\""+this.group+"\" style=\"display:none;\"><svg>"+this.contents+"</svg></div>");
			this.id=getId();
			$("#"+getId()).css('background',this.background);
			$("#"+getId()).css('background-size','cover');
			$("#"+getId()).css('position','fixed');
			this.compute = function() {
        		//Calculate the heighth
        		computedHeighth = this.heighth+this.hunit;
        		if(this.hunit == '%') {
        			tComputedHeighth = $(getAnchor(this.hanchor)).height() * (this.heighth / 100);
					computedHeighth = tComputedHeighth+'px';
				}
				//Calculate the width
        		computedWidth = this.width+this.wunit;
        		if(this.wunit == '%') {
        			tComputedWidth = $(getAnchor(this.wanchor)).width() * (this.width / 100);
					computedWidth = tComputedWidth+'px';
				}
				//Calculate the vpos
        		computedVpos = this.vpos+this.vposunit;
        		if(this.vposunit == '%') {
        			tComputedVpos = $(getAnchor(this.vpanchor)).position().top * (this.vpos / 100);
					computedVpos = tComputedVpos+'px';
				}
				//Calculate the hpos
        		computedHpos = this.hpos+this.hposunit;
        		if(this.hposunit == '%') {
        			tComputedHpos = $(getAnchor(this.hpanchor)).position().left * (this.hpos / 100);
					computedHpos = tComputedHpos+'px';
				}
			    $("#"+getId()).css('height',computedHeighth);
     	  		$("#"+getId()).css('width',computedWidth);
        		$("#"+getId()).css('top',computedVpos);
        		$("#"+getId()).css('left',computedHpos);
        	}
        	this.compute();
        	this.show = function(animation){
        		if(animation == "none") {
        			$("#"+getId()).css('display','block');
        		}
        		if(animation == "zoom") {
        			$("#"+getId()).css('top',($('body').height()/4)+'px');
        			$("#"+getId()).css('left',($('body').width()/4)+'px');
        			$("#"+getId()).css('width',($('body').width()/2)+'px');
        			$("#"+getId()).css('height',($('body').height()/2)+'px');
        			$("#"+getId()).css('opacity','0');
        			$("#"+getId()).show();
        			bodyWidth = $('body').width();
        			bodyHeighth = $('body').height();
        			$("#"+getId()).animate({
        				opacity: 1,
        				left: computedHpos,
        				top: computedVpos,
        				width: bodyWidth+"px",
        				height: bodyHeighth+"px"
        			}, 500, "linear");
        		}
        		if(animation == "fade") {
        			$("#"+getId()).css('opacity','0');
        			$("#"+getId()).show();
        			$("#"+getId()).animate({
        				opacity: 1,
        			}, 250, "linear");
        		}
        	};
        }
    var LoadingBox = new FluidBox("", "rgba(0,0,0,0)",0,0,0,50,"%",0,50,"%",0,10,"%",0,10,"%","loadingContainer",null);
	LoadingBox.show("fade"); 