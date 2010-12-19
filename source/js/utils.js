var Utils = function() { 
	
function parse_id(id) { 
	var index = id.indexOf("_");
	var len = id.length;
	var result = id.substr(index+1,len-1);
	return parseInt(result);
}

return {
	parse_id : parse_id
};

}();
