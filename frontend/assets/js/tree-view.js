var labelType, useGradients, nativeTextSupport, animate;

(function() {
  var ua = navigator.userAgent,
      iStuff = ua.match(/iPhone/i) || ua.match(/iPad/i),
      typeOfCanvas = typeof HTMLCanvasElement,
      nativeCanvasSupport = (typeOfCanvas == 'object' || typeOfCanvas == 'function'),
      textSupport = nativeCanvasSupport 
        && (typeof document.createElement('canvas').getContext('2d').fillText == 'function');
  //I'm setting this based on the fact that ExCanvas provides text support for IE
  //and that as of today iPhone/iPad current text support is lame
  labelType = (!nativeCanvasSupport || (textSupport && !iStuff))? 'Native' : 'HTML';
  nativeTextSupport = labelType == 'Native';
  useGradients = nativeCanvasSupport;
  animate = !(iStuff || !nativeCanvasSupport);
})();
function urlify(text) {
    var urlRegex = /(https?:\/\/[^\s]+)/g;
    return text.replace(urlRegex, function(url) {
        return '<a href="' + url + '">Link</a>';
    })
    // or alternatively
    // return text.replace(urlRegex, '<a href="$1">$1</a>')
}
function init(){
    //init data
    
    //end
    //init Spacetree
    //Create a new ST instance
    var st = new $jit.ST({
        //id of viz container element
        injectInto: 'tree-view',
        //set duration for the animation
        orientation:'top',
       offsetX: 0, offsetY: 180,

        duration: 800,
        //set animation transition type
        transition: $jit.Trans.Quart.easeInOut,
        //set distance between node and its children
        levelDistance: 50,
        //enable panning
        Navigation: {
          enable:true,
          panning:true
        },
        //set node and edge styles
        //set overridable=true for styling individual
        //nodes or edges
        Node: {
            height: 210,
            width: 220,
            type: 'rectangle',
            color: '#f0f0f0',
           
            
            overridable: false
        },
        
        Edge: {
            type: 'bezier',
             color: '#555',
            overridable: true
        },
        
        
        //This method is called on DOM label creation.
        //Use this method to add event handlers and styles to
        //your node.
        onCreateLabel: function(label, node){
            label.id = node.id;            
    
            var html = "<h4>" + node.data.content.truncate(45) + "</h4><br /><p style='padding-top:10px'>" + urlify(node.data.content_long.truncate(140)) + "</p>";  
            if(node.data.content_long.length>140){
				html+="<div id='div"+node.id+"' style='display:none'>"+node.data.content_long+"</div><a id='popup_"+node.id+"' style='margin-left:5px' onclick='$(\".modal-title\").html(\""+node.data.content+"\");$(\"#modal-content\").html($(\"#div"+node.id+"\").html());$(\"#myModal\").modal(\"show\");'>More</a>";
			}          
            label.innerHTML = html;
            
            label.onclick = function(){
             
                st.onClick(node.id);
              
            };
            //set label styles
            var style = label.style;
            style.width = 220 + 'px';
            style.height = 50 + 'px';            
            style.cursor = 'pointer';
            style.fontSize = '1em';
            style.textAlign= 'left';
            style.backgroundColor = '#3EAEAA';
            style.display = ''; 
        },
        
        //This method is called right before plotting
        //a node. It's useful for changing an individual node
        //style properties before plotting it.
        //The data properties prefixed with a dollar
        //sign will override the global node style properties.
        onBeforePlotNode: function(node){
            //add some color to the nodes in the path between the
            //root node and the selected node.
           // node.data.$color = "#fff";

            // if (node.selected) {
            //     node.data.$color = "#B7FFE6";
            // }
            // else {
            //     delete node.data.$color;
            //     //if the node belongs to the last plotted level
            //     if(!node.anySubnode("exist")) {
            //         //count children number
            //         var count = 0;
            //         node.eachSubnode(function(n) { count++; });
            //         //assign a node color based on
            //         //how many children it has
            //        // node.data.$color = ['#fff'][count];                    
            //     }
            //}
        },
        
        //This method is called right before plotting
        //an edge. It's useful for changing an individual edge
        //style properties before plotting it.
        //Edge data proprties prefixed with a dollar sign will
        //override the Edge global style properties.
        onBeforePlotLine: function(adj){
            if (adj.nodeFrom.selected && adj.nodeTo.selected) {
                adj.data.$color = "#3EAEAA";
                adj.data.$lineWidth = 3;
            }
            else {
                delete adj.data.$color;
                delete adj.data.$lineWidth;
            }
        }
    });
    if(json==null){$(".alert p").html("This treeview is empty");}
    //load json data
    st.loadJSON(json);
    //compute node positions and layout
    st.compute();
    //optional: make a translation of the tree
    st.geom.translate(new $jit.Complex(0, 100), "current");
    //emulate a click on the root node.
    st.onClick(st.root);
    //end
    //Add event handlers to switch spacetree orientation.
    var top = $jit.id('r-top'), 
        left = $jit.id('r-left'), 
        bottom = $jit.id('r-bottom'), 
        right = $jit.id('r-right'),
        normal = $jit.id('s-normal');
        
    
    function changeHandler() {
        if(this.checked) {
            top.disabled = bottom.disabled = right.disabled = left.disabled = true;
            st.switchPosition(this.value, "animate", {
                onComplete: function(){
                    top.disabled = bottom.disabled = right.disabled = left.disabled = false;
                }
            });
        }
    };
    
    // top.onchange = left.onchange = bottom.onchange = right.onchange = changeHandler;
    //end

}
