<div class="panel-group" id="accordion" data-toggle="collapse">
		<?php foreach($data[0]['contents'] as $key=>$item){ ?>
		<?php if($item['content']!=""){ ?>
		  <div class="panel panel-default">
			<div class="panel-heading collapsed">
			  <h4 class="panel-title">
				<a class="collapsed contentcollapse" name="<?=$data[0]['title']?>" data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$key?>">
				   <?=$item['title']?>&nbsp;
				</a>
			  </h4>
			</div>
			<div id="collapse<?=$key?>" class="panel-collapse collapse" style="height: 0px;">
			  <div class="panel-body">
				<?=$item['content']?>
			  </div>
			</div>
		  </div>
		  <?php }?>
		  <?php } ?>
		</div>
