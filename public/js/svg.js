
function svgCreateForeignObjectWithText( text, x, y, width, height, properties ) {
	let foreignObject = createForeignObject( x, y, width, height, properties );
	foreignObject.appendChild( document.createTextNode(text) );
	return foreignObject;
}


function svgCreateForeignObject( x, y, width, height, properties ) {
	let foreignObject = document.createElementNS(NS, 'foreignObject'); 
	foreignObject.setAttribute("x",x); 
	foreignObject.setAttribute("y",y); 
	foreignObject.setAttribute("width",width); 
	foreignObject.setAttribute("height",height); 
	if( 'id' in properties ) {
		foreignObject.setAttributeNS(null, 'id', properties.id );		
	} 
	if( 'fontSize' in properties ) {
		foreignObject.setAttributeNS(null,'font-size', properties.fontSize );
	}
	if( 'textAlign' in properties ) {
		foreignObject.setAttributeNS(null,'text-align', properties.textAlign );
	}
	if( 'color' in properties ) {
		foreignObject.setAttributeNS(null,'color', properties.color );
	}	
	return foreignObject;
}


function svgCreateRhomb( x, top, height, properties ) {
	return svgCreatePolygon( svgCalcRhombCoords(x, top, height), properties );
}


function svgCalcRhombCoords( x, top, height ) {
	let inc = 2;
	top -= inc;
	height += inc*2;
	let halfWidth = Math.floor(height / 2.0);
	let halfHeight = halfWidth;
	let points = (x - halfWidth) + " " + (top + halfHeight) + " " + x + " " + top;
	points += " " + (x + halfWidth) + " " + (top + halfHeight) + " " + x + " " + (top + height);
	return points;
}


function svgCreateRect( x, y, width, height, properties ) {
	let rect = document.createElementNS(NS, 'rect');
	if( 'id' in properties ) {
		rect.setAttributeNS(null, 'id', properties.id );		
	} 
	rect.setAttributeNS(null, 'x', x ); 
	rect.setAttributeNS(null, 'width', width ); 
	rect.setAttributeNS(null, 'y', y ); 
	rect.setAttributeNS(null, 'height', height );
	if( 'fill' in properties ) {
		rect.setAttributeNS(null, 'fill', properties.fill );
	} 
	if( 'stroke' in properties ) {
		rect.setAttributeNS(null, 'stroke', properties.stroke );
	}
	if( 'strokeWidth' in properties ) {
		rect.setAttributeNS(null, 'stroke-width', properties.strokeWidth );		 
	}
	if( 'opacity' in properties ) {
		rect.setAttributeNS(null, 'opacity', properties.opacity );
	} 
	return rect;
}

function svgSetRectCoords( rect, x, y, width, height ) {
	rect.setAttributeNS(null,'x',x);
	rect.setAttributeNS(null,'y',y);
	rect.setAttributeNS(null,'width',width);
	rect.setAttributeNS(null,'height',height);  
}

function svgCreatePolygon( points, properties ) {
	let polygon = document.createElementNS(NS, 'polygon');
	polygon.setAttributeNS(null, 'points', points );			
	if( 'id' in properties ) {
		polygon.setAttributeNS(null, 'id', properties.id );		 
	} 
	if( 'fill' in properties ) {
		polygon.setAttributeNS(null, 'fill', properties.fill );
	} 
	if( 'stroke' in properties ) {
		polygon.setAttributeNS(null, 'stroke', properties.stroke );
	}
	if( 'strokeWidth' in properties ) {
		polygon.setAttributeNS(null, 'stroke-width', properties.strokeWidth );		  
	}
	if( 'opacity' in properties ) {
		polygon.setAttributeNS(null, 'opacity', properties.opacity );
	} 
	return polygon;
}


function svgCreateText( textString, x, y, properties ) {
	let text = document.createElementNS(NS, 'text');
	text.setAttributeNS(null,'x', x );
	text.setAttributeNS(null,'y', y );
	if( 'id' in properties ) {
		let temp = document.getElementById(properties.id);
		text.setAttributeNS(null, 'id', properties.id );		

	} 
	if( 'fontSize' in properties ) {
		//text.setAttributeNS(null,'font-size', properties.fontSize );
		text.style.fontSize = properties.fontSize;
	}
	if( 'fontWeight' in properties ) {
		//text.setAttributeNS(null,'font-weight', properties.fontWeight );
		text.style.fontWeight = properties.fontWeight;
	}
	if( 'fontStyle' in properties ) {
		//text.setAttributeNS(null,'font-style', properties.fontStyle );		
		text.style.fontStyle = properties.fontStyle;
	}
	if( 'textAnchor' in properties ) {
		text.setAttributeNS(null,'text-anchor', properties.textAnchor );
	}
	if( 'textLength' in properties ) {
		if( properties.textLength ) {
			text.setAttributeNS(null,'textLength', properties.textLength );		 
		}
	}
	if( 'lengthAdjust' in properties ) {
		text.setAttributeNS(null,'lengthAdjust', properties.lengthAdjust );
	}
	if( 'alignmentBaseline' in properties ) {
		text.setAttributeNS(null,'alignment-baseline', properties.alignmentBaseline );
	}
	if( 'preserveAspectRatio' in properties ){
		text.setAttributeNS(null,'preserveAspectRatio', properties.preserveAspectRatio );
	}
	if( 'stroke' in properties) {
		text.setAttributeNS(null,'stroke', properties.stroke );
	}
	if( 'strokeWidth' in properties ) {
		text.setAttributeNS(null,'stroke-width', properties.strokeWidth );
	} else {
		text.setAttributeNS(null,'stroke-width', 0 );
	}
	if( 'fill' in properties ) {
		text.setAttributeNS(null,'fill', properties.fill );
	}
	if( 'clipPath' in properties ) {
		text.setAttributeNS(null,'clip-path', properties.clipPath );
	}
	text.appendChild( document.createTextNode( textString ) );
	return text;
}

function svgCreateLine( x1, y1, x2, y2, properties ) {
	let line = document.createElementNS(NS, 'line');
	if( 'id' in properties ) {
		line.setAttributeNS(null, 'id', properties.id );		
	} 
	if( 'endingArrow' in properties ) {
		if( properties.endingArrow ) {
			line.setAttributeNS(null,'marker-end', 'url(#arrow)');
		}
	}
	line.setAttributeNS(null, 'x1', x1 ); 
	line.setAttributeNS(null, 'y1', y1 ); 
	line.setAttributeNS(null, 'x2', x2 ); 
	line.setAttributeNS(null, 'y2', y2 );
	if( 'fill' in properties ) {
		line.setAttributeNS(null, 'fill', properties.fill );
	} 
	if( 'stroke' in properties ) {
		line.setAttributeNS(null, 'stroke', properties.stroke );
	}
	if( 'strokeWidth' in properties ) {
		line.setAttributeNS(null, 'stroke-width', properties.strokeWidth );		 
	}
	if( 'strokeDasharray' in properties ) {
		line.setAttributeNS(null, 'stroke-dasharray', properties.strokeDasharray );				 
	}
	if( 'opacity' in properties ) {
		line.setAttributeNS(null, 'opacity', properties.opacity );
	} 
	return line;
}


function svgCreateCircle( x, y, radius, properties ) {
	let circle = document.createElementNS(NS, 'circle');
	if( 'id' in properties ) {
		circle.setAttributeNS(null, 'id', properties.id );		
	} 
	circle.setAttributeNS(null, 'cx', x ); 
	circle.setAttributeNS(null, 'cy', y ); 
	circle.setAttributeNS(null, 'r', radius ); 
	if( 'fill' in properties ) {
		circle.setAttributeNS(null, 'fill', properties.fill );
	} 
	if( 'stroke' in properties ) {
		circle.setAttributeNS(null, 'stroke', properties.stroke );
	}
	if( 'strokeWidth' in properties ) {
		circle.setAttributeNS(null, 'stroke-width', properties.strokeWidth );		 
	}
	if( 'opacity' in properties ) {
		circle.setAttributeNS(null, 'opacity', properties.opacity );
	} 
	return circle;
}


function svgCreateContainer( x, y, width, height, properties ) {
	let svg = document.createElementNS(NS,'svg');
	svg.setAttributeNS(null,'x',x);
	svg.setAttributeNS(null,'y',y);
	svg.setAttributeNS(null,'width', width );
	svg.setAttributeNS(null,'height', height );
	if( 'fill' in properties ) {
		svg.setAttributeNS(null, 'fill', properties.fill);	  
	}
	if( 'id' in properties ) {
		svg.setAttributeNS(null, 'id', properties.id);	  
	}
	return svg; 
}


function svgCreateDefs( parentSVG ) {
	let defs = document.createElementNS(NS, 'defs');

	let gHarvesting = document.createElementNS(NS, 'g');
	gHarvesting.setAttribute('id', 'svgHarvestingIcon');
	let cb = svgCreateCircle( 10, -13, 7, { fill:'#4f4f4f' } );
	let cs = svgCreateCircle( 10, -13, 4, { fill:'#dfdfdf' } );
	let r = svgCreateRect( 0, -13, 20, 13, { fill:'#4f4f4f' } );
	gHarvesting.appendChild(cb);	
	gHarvesting.appendChild(cs);	
	gHarvesting.appendChild(r);	
	defs.appendChild(gHarvesting);   

	let gSupply = document.createElementNS(NS, 'g');
	gSupply.setAttribute('id', 'svgSupplyIcon');
	let r1 = svgCreateRect( 0, -20, 20, 15, { fill:'#4f4f4f' } );
	let r2 = svgCreateRect( 20, -12, 10, 8, { fill:'#4f4f4f' } );
	let c1 = svgCreateCircle( 8, -5, 5, { fill:'#4f4f4f' } );
	let c2 = svgCreateCircle( 22, -5, 5, { fill:'#4f4f4f' } );
	gSupply.appendChild(r1);	
	gSupply.appendChild(r2);	
	gSupply.appendChild(c1);	
	gSupply.appendChild(c2);		
	defs.appendChild(gSupply);   

	parentSVG.appendChild(defs);
}
