                var data = {
                  "name": "root",
                  "type":"Title of Root",
                  "children": [
                    {
                      "name": "Level 1",
                     "type":"Clinic Issue",
                      "url": "/#/about",
                      "task": true,
                      "children": []
                    },
                    {
                      "name": "Level 1",
                      "type":"Clinic Issue",
                      "children": [
                        {
                          "name": "Level 2",
                          "type":"Clinic Issue",
                          "url": "/#/about",
                          "children": []
                        },
                        {
                          "name": "Level 2",
                          "type":"Clinic Diagnosis",
                          "url": "/#/sandbox",
                          "children": []
                        },
                        {
                          "name": "Level 2",
                          "type":"Clinic Issue",
                          "url": "/#/sandbox",
                          "children": []
                        },
                        {
                          "name": "Level 2",
                          "type":"Clinic Issue",
                          "url": "/#/about",
                          "children": []
                        }
                      ]
                    },
                    {
                      "name": "Level 1",
                      "type":"Clinic Issue",
                      "url": "/#/sandbox",
                      "children": []
                    },
                    {
                      "name": "Level 1",
                      "type":"Clinic Issue",
                      "url": "/#/about",
                      "children": []
                    }
                  ]
                };

                var margin = {
                    top: 60,
                    right: 120,
                    bottom: 20,
                    left: 120
                },
                width = 960 - margin.right - margin.left,
                height = 800 - margin.top - margin.bottom;

                root = data;

                var i = 0;
                var duration = 500;
                var rectH = 140;
                var rectW = 150;

                var tree = d3.layout.tree().nodeSize([180, 50]);
                var diagonal = d3.svg.diagonal()
                    .projection(function (d) {
                    return [d.x + rectW / 2, d.y + rectH / 2];
                });

                var zm;
                var svg = d3.select(".tree-view").append("svg").attr("width", "100%").attr("height", 1000)
                    .call(zm = d3.behavior.zoom().scaleExtent([1,3]).on("zoom", redraw)).append("g")
                    .attr("transform", "translate(" + 330 + "," + 50 + ")");

                //necessary so that zoom knows where to zoom and unzoom from
                 zm.translate([350, 20]);

                root.x0 = 0;
                root.y0 = height / 2;

                function collapse(d) {
                    if (d.children) {
                        d._children = d.children;
                        d._children.forEach(collapse);
                        d.children = null;
                    }
                }

                root.children.forEach(collapse);
                update(root);

                d3.select(".tree-view").style("height", "100%");

                function update(source) {

                    // Compute the new tree layout.
                    var nodes = tree.nodes(root).reverse(),
                        links = tree.links(nodes);

                    // Normalize for fixed-depth.
                    nodes.forEach(function (d) {
                        d.y = d.depth * 180;
                    });

                    // Update the nodes…
                    var node = svg.selectAll("g.node")
                        .data(nodes, function (d) {
                        return d.id || (d.id = ++i);
                    });

                    // Enter any new nodes at the parent's previous position.
                    var nodeEnter = node.enter().append("g")
                        .attr("class", "node")
                        
                        .attr("transform", function (d) {
                        return "translate(" + source.x0 + "," + source.y0 + ")";
                    })

                    
                    nodeEnter.on("click", click);

                    nodeEnter.append("rect")
                        .attr("width", rectW)
                        .attr("height", rectH)
                        .attr("stroke", "black")
                        .attr("stroke-width", 1)
                        .style("fill", function (d) {
                        return d._children ? "#fff" : "#fff";
                    });

                    nodeEnter.append("rect")
                        .attr("width", rectW)
                        .attr("y", 0)
                        .attr("height", 25)
                        .attr("stroke", "black")
                        .attr("stroke-width", 1)
                        .style("fill", function (d) {
                        return d._children ? "#DDD" : "#DDD";
                    });

                 
                    nodeEnter.append("text")

                        .attr("x", rectW / 2)
                        .attr("y", rectH / 2)
                        .attr("dy", ".35em")
                        .attr("text-anchor", "middle")
                        .text(function (d) {
                        return d.name;
                    });

                    nodeEnter.append("text")
                        .attr("x", 10)
                        .attr("y", 16)
                        .style("font-weight","bold")
                        .style("font-size","13px")
                        .text(function (d) {
                        return d.type;
                    });


                    // Transition nodes to their new position.
                    var nodeUpdate = node.transition()
                        .duration(duration)
                        .attr("transform", function (d) {
                        return "translate(" + d.x + "," + d.y + ")";
                    });

                    nodeUpdate.select("rect")
                        .attr("width", rectW)
                        .attr("height", rectH)
                        .attr("stroke", "black")
                        .attr("stroke-width", 1)
                        .style("fill", function (d) {
                        return d._children ? "#fff" : "#fff";
                    });

                    nodeUpdate.select("text")
                        .style("fill-opacity", 1);

                    // Transition exiting nodes to the parent's new position.
                    var nodeExit = node.exit().transition()
                        .duration(duration)
                        .attr("transform", function (d) {
                        return "translate(" + source.x + "," + source.y + ")";
                    })
                        .remove();

                    nodeExit.select("rect")
                        .attr("width", rectW)
                        .attr("height", rectH)
                    //.attr("width", bbox.getBBox().width)""
                    //.attr("height", bbox.getBBox().height)
                    .attr("stroke", "black")
                        .attr("stroke-width", 1);

                    nodeExit.select("text");

                    // Update the links…
                    var link = svg.selectAll("path.link")
                        .data(links, function (d) {
                        return d.target.id;
                    });

                    // Enter any new links at the parent's previous position.
                    link.enter().insert("path", "g")
                        .attr("class", "link")
                        .attr("x", rectW / 2)
                        .attr("y", rectH / 2)
                        .attr("d", function (d) {
                        var o = {
                            x: source.x0,
                            y: source.y0
                        };
                        return diagonal({
                            source: o,
                            target: o
                        });
                    });

                    // Transition links to their new position.
                    link.transition()
                        .duration(duration)
                        .attr("d", diagonal);

                    // Transition exiting nodes to the parent's new position.
                    link.exit().transition()
                        .duration(duration)
                        .attr("d", function (d) {
                        var o = {
                            x: source.x,
                            y: source.y
                        };
                        return diagonal({
                            source: o,
                            target: o
                        });
                    })
                        .remove();

                    // Stash the old positions for transition.
                    nodes.forEach(function (d) {
                        d.x0 = d.x;
                        d.y0 = d.y;
                    });
                }

                // Toggle children on click.
                function click(d) {
                    if (d.children) {
                        d._children = d.children;
                        d.children = null;
                    } else {
                        d.children = d._children;
                        d._children = null;
                    }
                    update(d);
                }

                //Redraw for zoom
                function redraw() {
                  //console.log("here", d3.event.translate, d3.event.scale);
                  svg.attr("transform",
                      "translate(" + d3.event.translate + ")"
                      + " scale(" + d3.event.scale + ")");
                }
            

