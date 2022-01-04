<div class="panel-group" id="accordion" data-toggle="collapse">

	<?php foreach ($data[0]['contents'] as $key => $item) { ?>

		<?php if ($item['content'] != "") { ?>
			<div class="panel panel-default">
				<div class="panel-heading collapsed">
					<h4 class="panel-title">
						<a class="<?= (strpos($item['content'], $_SESSION['searchterm']) !== false ? '' : 'collapsed') ?> contentcollapse" name="<?= $data[0]['categories'] . "|" . $data[0]['title'] . "|" . $item['title'] ?>" data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $key ?>">
							<?= (isset($_SESSION['searchterm']) && $_SESSION['searchterm'] != '' ? preg_replace("/" . $_SESSION['searchterm'] . "/i", "<span class='highlight'>\$0</span>", $item['title']) : $item['title']) ?>&nbsp;
						</a>
					</h4>
				</div>
				<div id="collapse<?= $key ?>" class="panel-collapse <?= (strpos($item['content'], $_SESSION['searchterm']) !== false ? '' : 'collapse" style="height: 0px;') ?>">
					<div class="panel-body">
						<?= (isset($_SESSION['searchterm']) && $_SESSION['searchterm'] != '' ? preg_replace("/" . $_SESSION['searchterm'] . "/i", "<span class='highlight'>\$0</span>", $item['content']) : $item['content']) ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
</div>