var step = 0;
var todo = [];
var keep_moving = [true,true,true,true,true,true];
Height = 500;
Width = 1000;
Repulsion = 75;
Attraction = 25;
RepulsionSym = 55;
AttractionSym = 75;

function Start(){
	step=0;
	todo.splice(0,1);
	BuildSvgGraph();	
	Move();
}

function Move()
{
	setTimeout(Move, 120);
	RedrawGraphs();
};

function MouseEvent(e){

	svg_graph=todo[0];

	if (svg_graph.g.is3D){return};

	if (e.type == "mousedown" || e.type == "touchstart"){
		svg_graph.g.SetDragged(mouseX(e)-svg_graph.hw, mouseY(e)-svg_graph.hh, 30);
		};
	if (e.type == "mousemove" || e.type == "touchmove"){
		svg_graph.g.MoveDragged	(mouseX(e)-svg_graph.hw, mouseY(e)-svg_graph.hh);
		};
	if (e.type == "mouseup" || e.type == "touchend"){
		svg_graph.g.StopDragging();
		};
	if (e.type == "touchmove"){
		e.preventDefault();
		};
};

function RedrawGraphs()
{
	for (var i = 0; i < todo.length; i++) {		
		todo[i].g.Iterate();
		Redraw(todo[i]);
	}
}

function BuildSvgGraph()
{
	svg_id = "svg2";
	
	while (document.getElementById(svg_id).lastChild) {
    document.getElementById(svg_id).removeChild(document.getElementById(svg_id).lastChild);
	}
	
	nodes_labels = graph_nodes_labels;
	nodes_size = graph_nodes_size;
	
	svg_element = document.getElementById(svg_id);
	svg_graph = new SvgGraph(svg_element, graph_spec, nodes_labels, nodes_size);
	
	svg_graph.g.is3D = false;
	svg_graph.g.repulsion = 4*RepulsionSym;
	svg_graph.g.attraction = 0.001*AttractionSym;
	svg_graph.g.damping = 0.6;
	RebuildGraph(svg_graph);

	todo.push(svg_graph);	
	n = todo.length;

}

function SvgGraph(svg_element, spec, nodes_labels, nodes_size)
{
	this.spec = spec;
	this.svg = svg_element;
	
	var svg = this.svg;
	svg.addEventListener("mousedown", MouseEvent, false);
	svg.addEventListener("mousemove", MouseEvent, false);
	svg.addEventListener("mouseup", MouseEvent, false);
	
	svg.addEventListener("touchmove", MouseEvent, false);	
	svg.addEventListener("touchstart", MouseEvent, false);
	svg.addEventListener("touchend", MouseEvent, false);
	svg.addEventListener("touchmove", MouseEvent, false);
	
	
	this.c3d = { camz : 900, ang:0, d:0.003 };
	
	this.circs = [];
	this.lines = [];
	this.labls = [];
	this.labels_text = nodes_labels;
	
	this.w = Width-20;
	this.h = Height-20;
	this.hw= this.w/2;
	this.hh= this.h/2;
	this.labels = true;
	this.nodes_size = nodes_size;
	
	this.g = new Grapher2D();
	this.g.stable = false;
	this.g.physics = true;
	this.g.SetBounds(-this.hw,this.hw,-this.hh,this.hh,-this.hw,this.hw);
	
}

ChangeLabels = function(svg_graph) 
{
	svg_graph.labels = !svg_graph.labels;
	for(var i=0; i<svg_graph.labls.length; i++) svg_graph.labls[i].style.visibility = (svg_graph.labels?"visible":"hidden");
}

MinColoring = function(svg_graph) 
{
	svg_graph.g.MinColoring();
	for(var i=0; i<svg_graph.circs.length; i++)
		svg_graph.circs[i].setAttribute("fill", colors[svg_graph.g.vcolors[i]%colors.length]);
}

function RebuildGraph(svg_graph)
{

	svg_graph.g.MakeGraph(svg_graph.spec);
	
	var svg = svg_graph.svg;
	
	for(var i=0; i<svg_graph.circs.length; i++) svg.removeChild(this.circs[i]);
	for(var i=0; i<svg_graph.lines.length; i++) svg.removeChild(this.lines[i]);
	for(var i=0; i<svg_graph.labls.length; i++) svg.removeChild(this.labls[i]);
	svg_graph.circs = [];
	svg_graph.lines = [];
	svg_graph.labls = [];
	
	for(i=0; i<svg_graph.g.graph.edgesl.length; i++)
	{
		var l = document.createElementNS("http://www.w3.org/2000/svg", "line");
		l.setAttribute("style", "stroke:#000055;stroke-width:1");
		svg.appendChild(l);
		svg_graph.lines.push(l);
	}
	for(i=0; i<svg_graph.g.graph.n; i++)
	{
		var c = document.createElementNS("http://www.w3.org/2000/svg", "circle");
		c.setAttribute("fill", "RGB(80,255,0)");

		svg.appendChild(c);
		svg_graph.circs.push(c);
		
		var t = document.createElementNS("http://www.w3.org/2000/svg", "text");
		t.setAttribute("fill", "#000000");
		t.setAttribute("font-size", "13");
		t.setAttribute("style",  "pointer-events:none;");
		t.setAttribute("class", "GraphLabel");
		t.textContent = svg_graph.labels_text[i];
		svg.appendChild(t);
		svg_graph.labls.push(t);
	}
	
	Redraw(svg_graph);
};

function Redraw(svg_graph)
{
	//if(g.is3D) g.vertices.sort(sorter);
	
	var c3d = svg_graph.c3d;
	var g = svg_graph.g;

	
	c3d.ang += c3d.d;
	var sn = Math.sin(svg_graph.c3d.ang);
	var cs = Math.cos(svg_graph.c3d.ang);

	var hw = svg_graph.hw, hh = svg_graph.hh;

	for(var i=0; i<g.graph.n; i++)
	{
		var nx, ny, nz;
		var v = g.vertices[i];
		if(g.is3D)
		{
			nx = cs*v.x - sn*v.z;
			nz = sn*v.x + cs*v.z;
			ny = v.y;
		}
		else {nx = v.x; ny = v.y; nz = v.z;}
		v.px = c3d.camz*nx/(c3d.camz - nz);
		v.py = c3d.camz*ny/(c3d.camz - nz);
		v.pz = nz;

		// observe the borders
		if(v.px < -hw+15){v.px = -hw + 15};
		if(v.py < -hh+15){v.py = -hh + 15};
		if(v.px > hw-15){v.px = hw - 15};
		if(v.py > hh-15){v.py = hh - 15};
	};
	
	var num_edges = g.graph.edgesl.length;
	
	var N_switch = 120;
	var delta = 20;
	var selected = Math.floor(step/N_switch) % g.graph.n;
	var sel_step = step % N_switch;
	var fraction;
	if (sel_step <= delta){ fraction = sel_step / delta };
	if (sel_step > delta) { fraction = 1 };
	if (sel_step >= N_switch - delta){ fraction = (N_switch-sel_step)/delta };
	//alert(num_edges);
	for(i=0; i<num_edges; i++)
	{
		var u_num = g.graph.edgesl[i];
		var v_num = g.graph.edgesr[i];
		var u = g.vertices[u_num];
		var v = g.vertices[v_num];
		
		if(g.is3D){
			//allert("hello");
			if (u_num == selected || v_num==selected){
				brgh1 = String(57 + Math.round((255-57)*fraction));
				brgh2 = String(101 + Math.round((255-101)*fraction));
				line_color = "RGB(77," + brgh1 + "," + brgh2 + ")";
				stroke_width = "1.3";
			} else {
				line_color = "RGB(77,111,111)";		
				stroke_width = "1";
			};
		} else {
			//alert("hello");
			line_color = "RGB(0,0,0)";		
			stroke_width = "5.5";
		};
		
		svg_graph.lines[i].setAttribute("style", "stroke:" + line_color + ";stroke-width:" + graph_size_edges[i]);
		svg_graph.lines[i].setAttribute("x1", u.px + hw);
		svg_graph.lines[i].setAttribute("y1", u.py + hh);
		svg_graph.lines[i].setAttribute("x2", v.px + hw);
		svg_graph.lines[i].setAttribute("y2", v.py + hh);
	};
	
	var dr, circle_style;
	for(var i=0; i<g.graph.n; i++)
	{
		var v = g.vertices[i];
		//alert("hello");
		if(g.is3D){
			
			if (i==selected){
				circle_color = "RGB(77,255,255)";
				stroke_width = String(1.1 + fraction*0.2);
			} else {
				circle_color = "RGB(121,121,121)";	
				for(edge=0; edge<num_edges; edge++)
				{
					var u_num = g.graph.edgesl[edge];
					var v_num = g.graph.edgesr[edge];
					if( ((u_num == selected && v_num == i) || (v_num == selected && u_num == i)) 
						&& (sel_step < N_switch - delta) && (sel_step > delta) ){
						circle_color = "RGB(210,255,255)";	
					}
				};
				stroke_width = "1";
			};
			circle_style ="stroke:" + circle_color + ";stroke-width:" + stroke_width;
		} else {
			circle_color = "RGB(0,0,0)";		
			stroke_width = "1";
			circle_style ="stroke:" + circle_color + ";stroke-width:" + stroke_width + ";cursor:move;";
		};

		svg_graph.circs[i].setAttribute("style", circle_style);
		
		svg_graph.circs[i].setAttribute("cx", hw+v.px);
		svg_graph.circs[i].setAttribute("cy", hh+v.py);
		svg_graph.circs[i].setAttribute("r", svg_graph.nodes_size[i]);
		
		dr = 4 + (svg_graph.labels_text[i].length - 1)*3;
		svg_graph.labls[i].setAttribute("x", hw+v.px-dr);
		svg_graph.labls[i].setAttribute("y", hh+v.py+5);
	}
};


function getEl(s)
{
	return document.getElementById(s);
}


function getName(id)
{
	names_list_table = getEl("NamesList");
	row_index = parseInt(id)+1;
	res = names_list_table.rows[row_index].cells[1].textContent; 
	return res;
}


function mouseX(e)
{

	svg_graph=todo[0];

	var cx;
	if(e.type == "touchstart" || e.type == "touchmove") {
		cx = e.touches.item(0).clientX;
	} else {
		cx = e.clientX;
	};

	rect = svg_graph.svg.getBoundingClientRect();
	return (cx-rect.left);
}

function mouseY(e)
{	
	svg_graph=todo[0];

	var cy;
	if(e.type == "touchstart" || e.type == "touchmove")	{
		cy = e.touches.item(0).clientY;
	} else {
		cy = e.clientY;
	};

	rect = svg_graph.svg.getBoundingClientRect();
	return (cy-rect.top); 
}

