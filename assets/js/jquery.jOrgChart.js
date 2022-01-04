/**
 * jQuery org-chart/tree plugin.
 *
 * Author: Wes Nolte
 * http://twitter.com/wesnolte
 *
 * Based on the work of Mark Lee
 * http://www.capricasoftware.co.uk
 *
 * Copyright (c) 2011 Wesley Nolte
 * Dual licensed under the MIT and GPL licenses.
 *
 */
(function($) {
  var nodeTree="";
  var nodeCount = 0;
  $.fn.jOrgChart = function(options) {
    var opts = $.extend({}, $.fn.jOrgChart.defaults, options);
    var $appendTo = $(opts.chartElement);

    // build the tree
    $this = $(this);
    var $container = $("<div class='" + opts.chartClass + "'/>");
    if($this.is("ul")) {
      buildNode($this.find("li:first"), $container, 0 , 0, opts);
    }
    else if($this.is("li")) {
      buildNode($this, $container, 0, 0, opts);
    }



    $appendTo.append($container);

    // add drag and drop if enabled
    if(opts.dragAndDrop){
        $('div.node').draggable({
            cursor      : 'move',
            distance    : 40,
            helper      : 'clone',
            opacity     : 0.8,
            revert      : 'invalid',
            revertDuration : 100,
            snap        : 'div.node.expanded',
            snapMode    : 'inner',
            stack       : 'div.node'
        });

        $('div.node').droppable({
            accept      : '.node',
            activeClass : 'drag-active',
            hoverClass  : 'drop-hover'
        });

      // Drag start event handler for nodes
      $('div.node').bind("dragstart", function handleDragStart( event, ui ){

        var sourceNode = $(this);
        sourceNode.parentsUntil('.node-container')
                   .find('*')
                   .filter('.node')
                   .droppable('disable');
      });

      // Drag stop event handler for nodes
      $('div.node').bind("dragstop", function handleDragStop( event, ui ){

        /* reload the plugin */
        $(opts.chartElement).children().remove();
        $this.jOrgChart(opts);

      });

      // Drop event handler for nodes
      $('div.node').bind("drop", function handleDropEvent( event, ui ) {

        var targetID = $(this).data("tree-node");
        var targetLi = $this.find("li").filter(function() { return $(this).data("tree-node") === targetID; } );
        var targetUl = targetLi.children('ul');

        var sourceID = ui.draggable.data("tree-node");
        var sourceLi = $this.find("li").filter(function() { return $(this).data("tree-node") === sourceID; } );
        var sourceUl = sourceLi.parent('ul');

        if (targetUl.length > 0){
          targetUl.append(sourceLi);
        } else {
          targetLi.append("<ul></ul>");
          targetLi.children('ul').append(sourceLi);
        }

        //Removes any empty lists
        if (sourceUl.children().length === 0){
          sourceUl.remove();
        }
        var order="";
        $("#" + targetLi[0].id).children("ul").children().each(function(){
          order+=$(this).attr("id")+",";
        })
        order=order.replace(/,\s*$/, "");
        //console.log("Request: " + nodeTree.substring(1,nodeTree.length));
        nodeTree="";
        decisionID=$('#decision_id').val();
        $.get("/rest_v2/decisionmovenode/"+decisionID+"?token="+$("#token").val()+"&sourceid="+sourceLi[0].id+"&targetid="+targetLi[0].id+"&order="+order, function(data){
          status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
            return letter.toUpperCase();
          });
          if(status=="Success"){

          }
          else{
            alert(status+": "+data.response);
          }

        });
      }); // handleDropEvent

    } // Drag and drop
    $('.treeadd').click(function(e){
      e.preventDefault();
      parentNode="#"+$(this).data("id");
      token=$('#token').val();
      guidelineID=getURL('id');
      decisionID=$('#decision_id').val();
      decisionTitle=$('#decision_title').val();
      newID=getID()+1;
      $( "#dialog").html("<form id='addform' method='post' action='/rest_v2/decisionprocess/"+guidelineID+"'><input type='hidden' id='token' name='token' value='"+token+"'><input type='hidden' name='decision_id' value='"+decisionID+"'><input type='hidden' name='decision_title' value='"+decisionTitle+"'><input type='hidden' name='parentnode' value='"+$(this).data("id")+"'><input type='hidden' name='nodeid' value='"+newID+"'><input type='text' id='temptitle' name='title' placeholder='Title'><br /><br /><textarea id='tempcontent' name='content'></textarea></form>");
      //$('#tempcontent').ckeditor();
      CKEDITOR.replace( 'tempcontent' );
      $( "#dialog" ).dialog({
        title: "Add new node",
        resizable: true,
        height:500,
        width:800,
        modal: true,
        buttons: {
          "Submit": function() {
            $('#addform').ajaxSubmit({
              beforeSerialize: function(){
                $('#tempcontent').val(CKEDITOR.instances.tempcontent.getData());
              },
              beforeSubmit: function() {
                if ( $("#decision_title").val()==""){
                  alert("Decision title must be filled in.");
                  return false;
                }
              },
              success: function(data) {
                status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                  return letter.toUpperCase();
                });
                if(status=="Success"){
                  $('#decision_id').val(data.decision_id);
                  nodeHtml="<li id='"+newID+"'><div class='header content'>"+limit_text($('#temptitle').val(),30)+"</div><p class='content_long'>"+limit_text(stripHTML($('#tempcontent').val()),140)+"</p><div class='content_hidden' style='display:none'>"+$('#tempcontent').val()+"</div>";
                  if(opts.dragAndDrop){nodeHtml+="<div class='footer'><a href='#' class='treeadd' data-id='"+ newID + "'><i class='fa fa-plus-circle fa-2x'></i></a> <a href='#' class='treecopy' data-id='"+newID+"'><i class='fa fa-files-o fa-2x'></i></a> <a href='#' class='treeedit' data-id='"+newID+"'><i class='fa fa-pencil fa-2x'></i></a> <a href='#' class='treedelete' data-id='"+newID+"'><i class='fa fa-trash-o fa-2x'></i></a></div>";}
                  nodeHtml+="<ul></ul></li>";
                  $(parentNode + " ul:first").append(nodeHtml);
                  reloadChart();
                  $("#dialog").dialog( "close" );
                }
                else{
                  alert(status+": "+data.response);
                }
              }
            });
          },
          Cancel: function() {
            $( this ).dialog( "close" );
          }
        }
      });
    });

    $('.treecopy').click(function(e){
      e.preventDefault();
      copyNode= "#"+$(this).data("id");
      newID=getID()+1;
      targetNode="#"+newID;
      var oldids=$(this).data("id");
      var newids=newID;
      $(copyNode + " li").each(function(){
        oldids+="," + parseInt($(this).attr("id"));
      });
      $(copyNode).clone().attr("id",newID).appendTo($(copyNode).parent("ul"));
      $(targetNode).children("div.footer").children("a").each(function(){
          $(this).attr("data-id",newID);
        });
      $(targetNode + " li").each(function(){
        newID=getID()+1;
        $(this).attr("id",newID);
        $(this).children("div.footer").children("a").each(function(){
          $(this).attr("data-id",newID);
        });
        newids+="," + parseInt($(this).attr("id"));
      })
      decisionID=$('#decision_id').val();
      $.get("/rest_v2/decisionclonenode/"+decisionID+"?token="+$("#token").val()+"&oldid="+oldids+"&newid="+newids, function(data){
        status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
          return letter.toUpperCase();
        });
        if(status=="Success"){

        }
        else{
          alert(status+": "+data.response);
        }

      });
      reloadChart();
    });

    $('.treeedit').click(function(e){
      e.preventDefault();
      editNode= "#"+$(this).data("id");
      token=$('#token').val();
      guidelineID=getURL('id');
      decisionID=$('#decision_id').val();
      decisionTitle=$('#decision_title').val();
      parentNode=$(editNode).parent().parent().attr("id");
      nodeID=$(this).data("id");
      $( "#dialog").html("<form id='editform' method='post' action='/rest_v2/decisionprocess/"+guidelineID+"'><input type='hidden' id='token' name='token' value='"+token+"'><input type='hidden' name='decision_id' value='"+decisionID+"'><input type='hidden' name='decision_title' value='"+decisionTitle+"'><input type='hidden' name='parentnode' value='"+parentNode+"'><input type='hidden' name='nodeid' value='"+nodeID+"'><input type='text' id='temptitle' name='title' value='"+$(editNode + " .content").html() +"'><br /><br /><textarea id='tempcontent' name='content'>"+$(editNode + " .content_hidden").html() +"</textarea></form>");
      CKEDITOR.replace( 'tempcontent' );
      $( "#dialog" ).dialog({
        title: "Edit node",
        resizable: false,
        height:500,
        width:800,
        modal: true,
        buttons: {
          "Submit": function() {
            $('#editform').ajaxSubmit({
              beforeSerialize: function(){
                $('#tempcontent').val(CKEDITOR.instances.tempcontent.getData());
              },
              beforeSubmit: function(formData, jqForm, options) {
                if ( $("#decision_title").val()==""){
                  alert("Decision title must be filled in.");
                  return false;
                }
              },
              success: function(data) {
                status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                  return letter.toUpperCase();
                });
                if(status=="Success"){
                  $(editNode + " .content:first").html(limit_text($('#temptitle').val(),30));
                  $(editNode + " .content_long:first").html(limit_text(stripHTML($('#tempcontent').val()),140));
                  $(editNode + " .content_hidden:first").html($('#tempcontent').val());
                  reloadChart();
                  $("#dialog" ).dialog( "close" );
                }
                else{
                  alert(status+": "+data.response);
                }
              }
            });
          },
          Cancel: function() {
            $( this ).dialog( "close" );
          }
        }
      });
    });

    $('.treedelete').click(function(e){
      e.preventDefault();
      deleteNode= "#"+$(this).data("id");
      nodeID=$(this).data("id");
      decisionID=$('#decision_id').val();
      $( "#dialog").html("<p><span class='ui-icon ui-icon-alert' style='float:left; margin:0 7px 20px 0;'></span>These items will be permanently deleted and cannot be recovered. Are you sure?</p>");
      $( "#dialog" ).dialog({
        title: "Delete node and children",
        resizable: false,
        height:140,
        modal: true,
        buttons: {
          "Delete all items": function() {
            $.get("/rest_v2/decisiondeletenode/"+decisionID+"?token="+$("#token").val()+"&nodeid="+nodeID, function(data){
              status=data.status.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase();
              });
              if(status=="Success"){
                $(deleteNode).remove();
                reloadChart();
                $("#dialog" ).dialog( "close" );
              }
              else{
                alert(status+": "+data.response);
              }

            });

          },
          Cancel: function() {
            $( this ).dialog( "close" );
          }
        }
      });
    });

  };

  // Option defaults
  $.fn.jOrgChart.defaults = {
    chartElement : 'body',
    depth      : -1,
    chartClass : "jOrgChart",
    dragAndDrop: false
  };


  // Method that recursively builds the tree
  function buildNode($node, $appendTo, parentID, level, opts) {

    var $table = $("<table cellpadding='0' cellspacing='0' border='0'/>");
    var $tbody = $("<tbody/>");

    // Construct the node container(s)
    var $nodeRow = $("<tr/>").addClass("node-cells");
    var $nodeCell = $("<td/>").addClass("node-cell").attr("colspan", 2);
    var $childNodes = $node.children("ul:first").children("li");
    var $nodeDiv;
    var nodeID = parseInt($node.attr('id'));
    nodeTree +="&item_"+nodeID+"="+parentID
    if($childNodes.length > 1) {
      $nodeCell.attr("colspan", $childNodes.length * 2);
    }
    // Draw the node
    // Get the contents - any markup except li and ul allowed
    // add buttons
    $nodeContent = $node.clone()
                            .children("ul,li")
                            .remove()
                            .end()
                            .html();

      //Increaments the node count which is used to link the source list and the org chart
    nodeCount++;
    $node.data("tree-node", nodeCount);
    $nodeExpander="";

    $nodeExpander = $("<a>").addClass("treeexpand")
                            .append("Expand <i class='fa fa-arrow-circle-o-down'></i>");

    $nodeDiv = $("<div>").addClass("node")
                                     .data("tree-node", nodeCount)
                                     .html($nodeContent);

    // Expand and contract nodes
    if ($childNodes.length > 0) {
      $nodeDiv.append($nodeExpander);
      buildexpander($node,$nodeExpander,true);
      $nodeExpander.click(function() {
          buildexpander($node,$(this));
      });
    }

    $nodeCell.append($nodeDiv);
    $nodeRow.append($nodeCell);
    $tbody.append($nodeRow);

    if($childNodes.length > 0) {
      // if it can be expanded then change the cursor
      $nodeExpander.css('cursor','n-resize');

      // recurse until leaves found (-1) or to the level specified
      if(opts.depth == -1 || (level+1 < opts.depth)) {
        var $downLineRow = $("<tr/>");
        var $downLineCell = $("<td/>").attr("colspan", $childNodes.length*2);
        $downLineRow.append($downLineCell);

        // draw the connecting line from the parent node to the horizontal line
        $downLine = $("<div></div>").addClass("line down");
        $downLineCell.append($downLine);
        $tbody.append($downLineRow);

        // Draw the horizontal lines
        var $linesRow = $("<tr/>");
        $childNodes.each(function() {
          var $left = $("<td>&nbsp;</td>").addClass("line left top");
          var $right = $("<td>&nbsp;</td>").addClass("line right top");
          $linesRow.append($left).append($right);
        });

        // horizontal line shouldn't extend beyond the first and last child branches
        $linesRow.find("td:first")
                    .removeClass("top")
                 .end()
                 .find("td:last")
                    .removeClass("top");

        $tbody.append($linesRow);
        var $childNodesRow = $("<tr/>");
        $childNodes.each(function() {
           var $td = $("<td class='node-container'/>");
           $td.attr("colspan", 2);
           // recurse through children lists and items
           buildNode($(this), $td, nodeID, level+1, opts);
           $childNodesRow.append($td);
        });

      }
      $tbody.append($childNodesRow);
    }

    // any classes on the LI element get copied to the relevant node in the tree
    // apart from the special 'collapsed' class, which collapses the sub-tree at this point
    if ($node.attr('class') != undefined) {
        var classList = $node.attr('class').split(/\s+/);
        $.each(classList, function(index,item) {
            if (item == 'collapsed') {
                //console.log($node);
                $nodeRow.nextAll('tr').css('visibility', 'hidden');
                    $nodeRow.removeClass('expanded');
                    $nodeRow.addClass('contracted');
                    $nodeDiv.css('cursor','s-resize');
            } else {
                $nodeDiv.addClass(item);
            }
        });
    }

    // any id on the LI element get copied to the relevant node in the tree
    if ($node.attr('id') != undefined) {
      $nodeDiv.attr('id',"node_"+ nodeID);
    }



    $table.append($tbody);
    $appendTo.append($table);

    /* Prevent trees collapsing if a link inside a node is clicked */
    $nodeDiv.children('a').click(function(e){
        //console.log(e);
        e.stopPropagation();
    });
  };
  function buildexpander($node,element,start){
    var $this = element;
    var $tr = $this.closest("tr");

    if($tr.hasClass('contracted') || start==true){
      $this.css('cursor','n-resize');
      $tr.removeClass('contracted').addClass('expanded');
      $tr.nextAll("tr").css('visibility', '');
      $this.html("Collapse <i class='fa fa-arrow-circle-o-up'></i>");
      // Update the <li> appropriately so that if the tree redraws collapsed/non-collapsed nodes
      // maintain their appearance
      $node.removeClass('collapsed');
    }else{
      $this.css('cursor','s-resize');
      $tr.removeClass('expanded').addClass('contracted');
      $tr.nextAll("tr").css('visibility', 'hidden');
      $this.html("Expand <i class='fa fa-arrow-circle-o-down'></i>");
      $node.addClass('collapsed');
    }
  }
  function reloadChart(){
    $("#chart").html("");
    $("#org").jOrgChart({
        chartElement : '#chart',
        dragAndDrop  : true
    });
  }

  function getID(){

    tmpid=0;
    $( "#org li" ).each(function(){
      id=parseInt($(this).attr("id"));
      if(id>tmpid){tmpid=id;}
    });
    return tmpid;
  }

  function getURL(sParam){
      var sPageURL = window.location.search.substring(1);
      var sURLVariables = sPageURL.split('&');
      var sParameterValue="";
      for (var i = 0; i < sURLVariables.length; i++){
          var sParameterName = sURLVariables[i].split('=');
          if (sParameterName[0] == sParam) {
              sParameterValue=sParameterName[1];
          }
      }
      return sParameterValue;
  }

  function stripHTML(dirtyString) {
    var container = document.createElement('div');
    container.innerHTML = dirtyString;
    return container.textContent || container.innerText;
  }

  function limit_text(text, limit) {
    if (text.length > limit) {
      text = text.substring(0,limit)+"...";
    }
    return text;
  }

})(jQuery);
